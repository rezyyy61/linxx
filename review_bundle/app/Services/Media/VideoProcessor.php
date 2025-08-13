<?php

namespace App\Services\Media;

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

    /**
     * پردازش ویدیو و ساخت HLS + thumbnail هوشمند بدون padding
     */
    public function process(\SplFileInfo|string $file, int $postId): array
    {
        /* دیسک‌ها */
        $tmpDisk    = 'local';
        $publicDisk = 'public';

        /* آپلود ورودی به tmp */
        $uuid       = (string) Str::uuid();
        $extension  = is_string($file) ? pathinfo($file, PATHINFO_EXTENSION) : $file->getExtension();
        $tmpInput   = "tmp/{$uuid}_src.{$extension}";
        Storage::disk($tmpDisk)->put($tmpInput, fopen(is_string($file) ? $file : $file->getRealPath(), 'rb'));
        $absInput   = Storage::disk($tmpDisk)->path($tmpInput);
        if (!is_file($absInput) || filesize($absInput) === 0) {
            throw new \RuntimeException('Upload to tmp failed: '.$absInput);
        }

        /* ساخت HLS */
        $hlsDir    = "tmp/{$uuid}_hls";
        $absHlsDir = Storage::disk($tmpDisk)->path($hlsDir);
        mkdir($absHlsDir, 0755, true);

        // فیلترهای scale بدون padding
        $filter1080 = "scale='min(1920,iw)':-2";
        $filter720  = "scale='min(1280,iw)':-2";
        $filter480  = "scale='min(854,iw)':-2";
        $filter360  = "scale='min(640,iw)':-2";
        $filterComplex = "[0:v]split=4[v1080][v720][v480][v360];"
            . "[v1080]{$filter1080}[out1080];"
            . "[v720]{$filter720}[out720];"
            . "[v480]{$filter480}[out480];"
            . "[v360]{$filter360}[out360]";

        $cmd = "/usr/bin/ffmpeg -y -i {$absInput} -preset veryfast -g 48 -sc_threshold 0 "
            . "-filter_complex \"{$filterComplex}\" "
            . "-map [out1080] -map 0:a:0 -b:v:0 5000k "
            . "-map [out720]  -map 0:a:0 -b:v:1 3000k "
            . "-map [out480]  -map 0:a:0 -b:v:2 1500k "
            . "-map [out360]  -map 0:a:0 -b:v:3 800k "
            . "-c:v h264 -profile:v high -level 4.2 -c:a aac -ar 48000 -ac 2 "
            . "-var_stream_map \"v:0,a:0 v:1,a:1 v:2,a:2 v:3,a:3\" "
            . "-hls_time 6 -hls_playlist_type vod "
            . "-hls_segment_filename {$absHlsDir}/v%v_%03d.ts {$absHlsDir}/v%v.m3u8";

        exec($cmd, $out, $res);
        if ($res !== 0) {
            Log::error('FFmpeg HLS generation failed', ['cmd' => $cmd, 'output' => $out]);
            throw new \RuntimeException('HLS generation failed');
        }

        // master playlist
        file_put_contents("{$absHlsDir}/master.m3u8", "#EXTM3U\n"
            . "#EXT-X-STREAM-INF:BANDWIDTH=5000000,RESOLUTION=1920x1080\nv0.m3u8\n"
            . "#EXT-X-STREAM-INF:BANDWIDTH=3000000,RESOLUTION=1280x720\nv1.m3u8\n"
            . "#EXT-X-STREAM-INF:BANDWIDTH=1500000,RESOLUTION=854x480\nv2.m3u8\n"
            . "#EXT-X-STREAM-INF:BANDWIDTH=800000,RESOLUTION=640x360\nv3.m3u8\n");

        /* متادیتا */
        $format      = $this->ffprobe->format($absInput);
        $duration    = (int) $format->get('duration');
        $stream      = $this->ffprobe->streams($absInput)->videos()->first();
        $origWidth   = $stream?->get('width')  ?? 0;
        $origHeight  = $stream?->get('height') ?? 0;
        $aspectRatio = $origHeight ? round($origWidth / $origHeight, 2) : 1.0;
        $orientation = $aspectRatio < 0.9 ? 'portrait' : ($aspectRatio > 1.1 ? 'landscape' : 'square');
        $resolutions = ['1080p', '720p', '480p', '360'];

        /* thumbnail هوشمند */
        $thumbTmp = Storage::disk($tmpDisk)->path("tmp/{$uuid}.jpg");

        // تلاش اول: فیلتر thumbnail که از بین 100 فریم اول بهترین رو می‌گیره
        $cmdThumb = "/usr/bin/ffmpeg -y -i {$absInput} -vf \"thumbnail,scale='min(1280,iw)':-2\" -frames:v 1 {$thumbTmp}";
        exec($cmdThumb, $oT, $rT);

        // اگر تولید نشد یا خیلی کم‌حجم بود، fallback به ثانیه‌های خاص
        if ($rT !== 0 || !file_exists($thumbTmp) || filesize($thumbTmp) < 10240) {
            $candidates = [3, 5, 10, 15, 20, intval($duration / 2)];
            foreach ($candidates as $sec) {
                $cmdTmp = "/usr/bin/ffmpeg -y -ss {$sec} -i {$absInput} -vframes 1 -vf scale='min(1280,iw)':-2 -q:v 2 {$thumbTmp}";
                exec($cmdTmp, $oo, $rr);
                if ($rr === 0 && file_exists($thumbTmp) && filesize($thumbTmp) >= 10240) {
                    break;
                }
            }
        }

        Storage::disk($publicDisk)->put("posts/{$postId}/{$uuid}.jpg", fopen($thumbTmp, 'rb'));

        /* انتقال HLS به public */
        $hlsPublic = "posts/{$postId}/hls";
        foreach (glob("{$absHlsDir}/*") as $seg) {
            Storage::disk($publicDisk)->put("{$hlsPublic}/".basename($seg), file_get_contents($seg));
        }

        /* پاکسازی */
        Storage::disk($tmpDisk)->delete([$tmpInput]);
        @unlink($thumbTmp);
        array_map('unlink', glob("{$absHlsDir}/*"));
        @rmdir($absHlsDir);

        return [
            'path'            => "{$hlsPublic}/master.m3u8",
            'hls_path'        => "{$hlsPublic}/master.m3u8",
            'thumb_path'      => "posts/{$postId}/{$uuid}.jpg",
            'duration'        => $duration,
            'width'           => $origWidth,
            'height'          => $origHeight,
            'original_width'  => $origWidth,
            'original_height' => $origHeight,
            'aspect_ratio' => $aspectRatio,
            'orientation' => $orientation,
            'resolutions_available' => $resolutions,
        ];
    }
}
