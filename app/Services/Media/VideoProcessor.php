<?php

namespace App\Services\Media;

use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg;
use FFMpeg\FFProbe;
use FFMpeg\Format\Video\X264;
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
     * @param \SplFileInfo|string $file
     * @param int $postId
     */
    public function process(\SplFileInfo|string $file, int $postId): array
    {
        $tmpDisk    = 'local';
        $publicDisk = 'public';

        $uuid      = (string) Str::uuid();
        $extension = is_string($file) ? pathinfo($file, PATHINFO_EXTENSION) : $file->getExtension();
        $tmpInput  = "{$uuid}_src.".$extension;
        $tmpOutput = "{$uuid}.mp4";
        $tmpThumb  = "{$uuid}.jpg";

        $inputPath = "tmp/{$tmpInput}";

        Storage::disk($tmpDisk)->put($inputPath, fopen(is_string($file) ? $file : $file->getRealPath(), 'rb'));

        $absInput  = Storage::disk($tmpDisk)->path($inputPath);
        $absOutput = Storage::disk($tmpDisk)->path("tmp/{$tmpOutput}");
        $absThumb  = Storage::disk($tmpDisk)->path("tmp/{$tmpThumb}");

        if (! is_file($absInput) || filesize($absInput) === 0) {
            throw new \RuntimeException('Upload to tmp failed: '.$absInput);
        }

        $format = (new X264('aac', 'libx264'))
            ->setKiloBitrate(1500)
            ->setAdditionalParameters(['-movflags', '+faststart']);

        try {
            $this->ffmpeg->open($absInput)->save($format, $absOutput);
        } catch (\Throwable $e) {
            Log::error('FFmpeg encode error', ['message' => $e->getMessage()]);
            throw $e;
        }

        $this->ffmpeg->open($absOutput)
            ->frame(TimeCode::fromSeconds(1))
            ->save($absThumb);

        $meta     = $this->ffprobe->format($absOutput);
        $duration = (int) $meta->get('duration');
        $stream   = $this->ffprobe->streams($absOutput)->videos()->first();
        $width    = $stream?->get('width')  ?? 0;
        $height   = $stream?->get('height') ?? 0;
        $size     = filesize($absOutput);

        $folder   = "posts/{$postId}";
        $videoRel = "{$folder}/{$uuid}.mp4";
        $thumbRel = "{$folder}/{$uuid}.jpg";

        Storage::disk($publicDisk)->put($videoRel, fopen($absOutput, 'rb'));
        Storage::disk($publicDisk)->put($thumbRel, fopen($absThumb, 'rb'));

        Storage::disk($tmpDisk)->delete([$inputPath, "tmp/{$tmpOutput}", "tmp/{$tmpThumb}"]);

        return [
            'video_path' => $videoRel,
            'thumb_path' => $thumbRel,
            'duration'   => $duration,
            'width'      => $width,
            'height'     => $height,
            'size'       => $size,
        ];
    }
}
