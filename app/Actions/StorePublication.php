<?php

// app/Actions/StorePublication.php
namespace App\Actions;

use App\Models\PoliticalProfile;
use App\Services\FileUploader;
use Illuminate\Http\UploadedFile;

class StorePublication
{
    public function __construct(
        private FileUploader    $uploader,
    ) {}

    /**
     * @param  PoliticalProfile $profile
     * @param  UploadedFile     $file
     * @param  array            $data
     */
    public function handle(PoliticalProfile $profile, UploadedFile $file, array $data)
    {
        $path = $this->uploader->upload($file, 'publications');

        return $profile->publications()->create([
            'title'        => $data['title'],
            'issue'        => $data['issue'],
            'description'  => $data['description'],
            'published_at' => $data['published_at'],
            'file_path'    => $path,
            'file_type'    => $file->getClientOriginalExtension(),
        ]);
    }
}
