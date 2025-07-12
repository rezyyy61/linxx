<?php

namespace App\Jobs;

use App\Models\PostMedia;
use App\Services\Media\AudioProcessor;
use App\Services\Media\ImageProcessor;
use App\Services\Media\VideoProcessor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\File;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProcessPostMedia implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var int */
    public $timeout = 3600;

    /** @var int */
    public $tries   = 3;

    public function __construct(
        public int $postMediaId,
    ) {
    }

    public function handle(
        ImageProcessor $imageProcessor,
        VideoProcessor $videoProcessor,
        AudioProcessor $audioProcessor
    ): void
    {
        $media = PostMedia::find($this->postMediaId);
        if (! $media) {
            Log::error('PostMedia not found', ['id' => $this->postMediaId]);
            return; }

        $tmpPath = $media->meta_tmp['tmp_path'] ?? null;

        $absTmp = Storage::disk('local')->path($tmpPath);

        if (! $tmpPath || ! Storage::disk('local')->exists($tmpPath)) {
            $media->update(['status' => 'failed', 'error' => 'Tmp file not found']);
            Log::error('Tmp file not found', ['id' => $this->postMediaId]);
            return;
        }

        try {
            $meta = match ($media->type) {
                'image' => $imageProcessor->process(new File($absTmp), $media->post_id),
                'video' => $videoProcessor->process(new File($absTmp), $media->post_id),
                'audio' => $audioProcessor->process(new File($absTmp), $media->post_id),
                default => throw new \RuntimeException('Unsupported media type'),
            };

            $media->update([
                'path'     => $meta['path']
                    ?? $meta['video_path']
                        ?? $meta['audio_path'],
                'duration' => $meta['duration'] ?? null,
                'meta'     => $meta,
                'status'   => 'done',
                'error'    => null,
            ]);

        } catch (\Throwable $e) {
            report($e);
            $media->update(['status' => 'failed', 'error' => $e->getMessage()]);
            Log::error('ProcessPostMedia error', ['id' => $this->postMediaId, 'message' => $e->getMessage()]);
            throw $e;
        } finally {
            Storage::disk('local')->delete($tmpPath);
        }
    }
}
