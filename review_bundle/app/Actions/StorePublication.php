<?php

namespace App\Actions;

use App\Models\Post;
use App\Models\PostMedia;
use App\Models\PoliticalProfile;
use App\Services\FileUploader;
use App\Services\Media\FileScanner;
use Illuminate\Http\UploadedFile;

class StorePublication
{
    public function __construct(
        private FileUploader $uploader,
        private FileScanner $scanner,
    ) {}

    /**
     * Handle storing a publication and creating a related post with media.
     *
     * @param  PoliticalProfile $profile
     * @param  UploadedFile     $file
     * @param  array            $data
     * @return \App\Models\Publication
     */
    public function handle(PoliticalProfile $profile, UploadedFile $file, array $data)
    {
        try {
            $this->scanner->isClean($file->getRealPath());
        } catch (\Exception $e) {
            throw new \RuntimeException("Upload failed: " . $e->getMessage());
        }

        // Upload file to storage
        $path = $this->uploader->upload($file, 'publications');

        // Create the publication
        $publication = $profile->publications()->create([
            'title'        => $data['title'],
            'issue'        => $data['issue'],
            'description'  => $data['description'],
            'published_at' => $data['published_at'],
            'file_path'    => $path,
            'file_type'    => $file->getClientOriginalExtension(),
        ]);

        // Create related post (only with title or description)
        $post = Post::create([
            'user_id'    => $profile->user_id,
            'text'       => $data['description'] ?? $data['title'],
            'visibility' => 'public',
        ]);

        // Attach media to post with meta info about the publication
        PostMedia::create([
            'post_id' => $post->id,
            'type'    => 'file',
            'path'    => $path,
            'meta'    => [
                'source'       => 'publication',
                'title'        => $data['title'],
                'issue'        => $data['issue'],
                'published_at' => $data['published_at'],
                'file_type'    => $file->getClientOriginalExtension(),
                'file_name'    => $file->getClientOriginalName(),
            ],
            'status' => 'done',
        ]);

        $publication->update([
            'post_id' => $post->id
        ]);

        return $publication;
    }
}
