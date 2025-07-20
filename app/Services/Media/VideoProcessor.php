<?php

namespace App\Services\Media;

use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg;
use FFMpeg\FFProbe;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VideoProcessor
{
    protected FFMpeg  $ffmpeg;
    protected FFProbe $ffprobe;

    public function __construct()
    {
        $cfg = [
            'timeout'          => 3600,
            'ffmpeg.binaries'  => '/usr/bin/ffmpeg',
            'ffprobe.binaries' => '/usr/bin/ffprobe',
        ];

        $this->ffmpeg  = FFMpeg::create($cfg);
        $this->ffprobe = FFProbe::create($cfg);
    }

    public function process(\SplFileInfo|string $file, int $postId): array
    {
        $tmpDisk    = 'local';
        $publicDisk = 'public';

        $uuid      = (string) Str::uuid();
        $extension = is_string($file) ? pathinfo($file, PATHINFO_EXTENSION) : $file->getExtension();
        $tmpInput  = "tmp/{$uuid}_src." . $extension;
        $tmpThumb  = "tmp/{$uuid}.jpg";

        // 1. ذخیره فایل ویدیویی آپلود شده در حافظه موقت
        Storage::disk($tmpDisk)->put($tmpInput, fopen(is_string($file) ? $file : $file->getRealPath(), 'rb'));

        $absInput = Storage::disk($tmpDisk)->path($tmpInput);
        $absThumb = Storage::disk($tmpDisk)->path($tmpThumb);

        if (!is_file($absInput) || filesize($absInput) === 0) {
            throw new \RuntimeException('Upload to tmp failed: ' . $absInput);
        }

        // 2. مسیر پوشه خروجی HLS موقتی
        $hlsDir    = "tmp/{$uuid}_hls";
        $absHlsDir = Storage::disk($tmpDisk)->path($hlsDir);
        mkdir($absHlsDir, 0755, true);

        // 3. فیلترهایی برای scale + pad به سایز مشخص (1280x720 و 854x480)
        $filter720 = "scale=iw*min(1280/iw\\,720/ih):ih*min(1280/iw\\,720/ih),pad=1280:720:(1280-iw*min(1280/iw\\,720/ih))/2:(720-ih*min(1280/iw\\,720/ih))/2";
        $filter480 = "scale=iw*min(854/iw\\,480/ih):ih*min(854/iw\\,480/ih),pad=854:480:(854-iw*min(854/iw\\,480/ih))/2:(480-ih*min(854/iw\\,480/ih))/2";

        // 4. دستور ffmpeg برای ساخت دو استریم HLS
        $cmd = "/usr/bin/ffmpeg -y -i {$absInput} -preset veryfast -g 48 -sc_threshold 0 "
            . "-filter_complex \"[0:v]split=2[v1][v2];"
            . "[v1]{$filter720}[out1];"
            . "[v2]{$filter480}[out2]\" "
            . "-map [out1] -map 0:a:0 -map [out2] -map 0:a:0 "
            . "-b:v:0 3000k -b:v:1 1500k "
            . "-c:v h264 -c:a aac -ar 48000 -ac 2 "
            . "-var_stream_map \"v:0,a:0 v:1,a:1\" "
            . "-f hls -hls_time 6 -hls_playlist_type vod "
            . "-hls_segment_filename {$absHlsDir}/v%v_%03d.ts "
            . "{$absHlsDir}/v%v.m3u8";

        exec($cmd, $output, $result);

        Log::info('✅ FFMPEG HLS created with padding', [
            'cmd'    => $cmd,
            'output' => $output,
            'result' => $result,
        ]);

        if ($result !== 0) {
            Log::error('FFmpeg HLS generation failed', ['output' => $output]);
            throw new \RuntimeException('HLS generation failed');
        }

        // 5. ساخت فایل master.m3u8 که لیست کیفیت‌ها را مشخص می‌کند
        $master = <<<EOT
#EXTM3U
#EXT-X-STREAM-INF:BANDWIDTH=3000000,RESOLUTION=1280x720
v0.m3u8
#EXT-X-STREAM-INF:BANDWIDTH=1500000,RESOLUTION=854x480
v1.m3u8
EOT;
        file_put_contents("{$absHlsDir}/master.m3u8", $master);

        // 6. ساخت thumbnail از ثانیه اول ویدیو
        $this->ffmpeg->open($absInput)
            ->frame(TimeCode::fromSeconds(1))
            ->save($absThumb);

        // 7. گرفتن اطلاعات از ویدیو اصلی (برای original width/height فقط)
        $meta         = $this->ffprobe->format($absInput);
        $duration     = (int) $meta->get('duration');
        $stream       = $this->ffprobe->streams($absInput)->videos()->first();
        $origWidth    = $stream?->get('width') ?? 0;
        $origHeight   = $stream?->get('height') ?? 0;

        // 8. انتقال همه فایل‌های HLS به public/posts/{id}/hls
        $folder = "posts/{$postId}/hls";
        $publicPath = Storage::disk($publicDisk)->path($folder);
        @mkdir($publicPath, 0755, true);

        foreach (glob("{$absHlsDir}/*") as $segment) {
            $filename = basename($segment);
            Storage::disk($publicDisk)->put("{$folder}/{$filename}", file_get_contents($segment));
        }

        // 9. ذخیره thumbnail
        $thumbRel = "posts/{$postId}/{$uuid}.jpg";
        Storage::disk($publicDisk)->put($thumbRel, fopen($absThumb, 'rb'));

        // 10. حذف فایل‌های موقت
        Storage::disk($tmpDisk)->delete([$tmpInput, $tmpThumb]);
        foreach (glob("{$absHlsDir}/*") as $f) {
            unlink($f);
        }
        rmdir($absHlsDir);

        // 11. ثبت لاگ خروجی نهایی
        Log::info('VideoProcessor HLS Output:', [
            'path'       => "{$folder}/master.m3u8",
            'hls_path'   => "{$folder}/master.m3u8",
            'thumb_path' => $thumbRel,
            'duration'   => $duration,
            'width'      => 1280, // سایز خروجی ثابت HLS
            'height'     => 720,
            'original_width'  => $origWidth,
            'original_height' => $origHeight,
        ]);

        // 12. خروجی نهایی برای ذخیره در دیتابیس
        return [
            'path'             => "{$folder}/master.m3u8",
            'hls_path'         => "{$folder}/master.m3u8",
            'thumb_path'       => $thumbRel,
            'duration'         => $duration,
            'width'            => 1280,
            'height'           => 720,
            'original_width'   => $origWidth,
            'original_height'  => $origHeight,
        ];
    }
}
