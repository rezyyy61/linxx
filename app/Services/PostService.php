<?php

namespace App\Services;

use App\Events\PostQueued;
use App\Events\PostReady;
use App\Jobs\ProcessPostMedia;
use App\Models\Post;
use App\Models\PostMedia;
use App\Services\Media\FileScanner;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PostService
{
    public function __construct(
        protected FileScanner $scanner,
        protected string $tmpDisk = 'local'
    ) {}

    public function createPost(array $data, array $media, $user): Post
    {
        return DB::transaction(function () use ($data, $media, $user) {

            $post = Post::create([
                'user_id'    => $user->id,
                'text'       => $data['text'] ?? null,
                'visibility' => 'public',
            ]);

            PostQueued::dispatch($post);

            $this->storeMedia($post, $media);

            return $post->load('media');
        });
    }

    protected function storeMedia(Post $post, array $media): void
    {
        $order   = 0;
        $typeMap = [
            'images' => 'image',
            'videos' => 'video',
            'files'  => 'file',
            'audio'  => 'audio',
        ];

        foreach ($media as $group => $files) {
            $type = $typeMap[$group] ?? $group;

            foreach ($files as $file) {
                $uuidName = Str::uuid() . '.' . $file->getClientOriginalExtension();
                $tmpPath = $file->storeAs('tmp', $uuidName, $this->tmpDisk);

                $postMedia = PostMedia::create([
                    'post_id'  => $post->id,
                    'type'     => $type,
                    'order'    => $order++,
                    'status'   => 'pending',
                    'path'     => $tmpPath,
                    'meta_tmp' => ['tmp_path' => $tmpPath],
                ]);

                if (in_array($type, ['image', 'video', 'audio'])) {
                    ProcessPostMedia::dispatch($postMedia->id)->onQueue('media');
                } elseif ($type === 'file') {
                    $absTmp = Storage::disk($this->tmpDisk)->path($tmpPath);

                    if (! $this->scanner->isClean($absTmp)) {
                        Storage::disk($this->tmpDisk)->delete($tmpPath);
                        $postMedia->update(['status' => 'failed', 'error' => 'virus detected']);
                        continue;
                    }

                    $finalPath = "posts/{$post->id}/{$uuidName}";

                    Storage::disk('public')->put($finalPath, Storage::disk($this->tmpDisk)->get($tmpPath));
                    Storage::disk($this->tmpDisk)->delete($tmpPath);

                    $postMedia->update([
                        'path'   => $finalPath,
                        'status' => 'done',
                        'meta'   => [
                            'size'          => Storage::disk('public')->size($finalPath),
                            'original_name' => $file->getClientOriginalName(),
                            'extension'     => $file->getClientOriginalExtension(),
                        ],
                    ]);
                }
            }
        }

        $this->checkAndBroadcastIfReady($post);
    }

    public function checkAndBroadcastIfReady(Post $post): void
    {
        if ($post->media()->whereIn('status', ['pending', 'processing'])->doesntExist()) {
            $post->update(['status' => 'ready']);
            broadcast(new PostReady(
                $post->fresh(['media', 'user'])
            ));
        }
    }
}
