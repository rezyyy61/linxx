<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class FileUploader
{
    public function upload(UploadedFile $file, string $folder): string
    {
        $name = time().'_'.Str::random(10).'.'.$file->getClientOriginalExtension();
        return $file->storeAs($folder, $name, 'public');
    }
}
