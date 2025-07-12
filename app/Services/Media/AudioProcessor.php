<?php
namespace App\Services\Media;

use FFMpeg\FFMpeg;
use FFMpeg\FFProbe;
use FFMpeg\Format\Audio\Mp3;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AudioProcessor
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
        $publicDisk = 'public';

        $uuid        = (string) Str::uuid();
        $tmpOutput   = "{$uuid}.mp3";

        $absInput  = is_string($file) ? $file : $file->getRealPath();

        $absOutput = sys_get_temp_dir().DIRECTORY_SEPARATOR.$tmpOutput;

        $format = (new Mp3())->setAudioKiloBitrate(128)->setAudioChannels(2);

        try {
            $this->ffmpeg->open($absInput)->save($format, $absOutput);
        } catch (\Throwable $e) {
            Log::error('FFmpeg audio encode error', ['message' => $e->getMessage()]);
            throw $e;
        }

        // دریافت متادیتا
        $meta     = $this->ffprobe->format($absOutput);
        $duration = (int) $meta->get('duration');
        $size     = filesize($absOutput);

        // ذخیره در public
        $folder    = "posts/{$postId}";
        $audioRel  = "{$folder}/{$uuid}.mp3";

        Storage::disk($publicDisk)->put($audioRel, fopen($absOutput, 'rb'));

        // پاکسازی فایل tmp خروجی
        unlink($absOutput);

        return [
            'audio_path' => $audioRel,
            'duration'   => $duration,
            'size'       => $size,
            'format'     => 'mp3',
            'bitrate'    => 128,
        ];
    }
}
