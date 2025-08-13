<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PostMedia;
use Illuminate\Support\Facades\Storage;

class MediaDownloadController extends Controller
{
    public function show(int $id)
    {
        $media = PostMedia::findOrFail($id);

        $disk = Storage::disk('public');
        abort_unless($disk->exists($media->path), 404);

        $name = $media->meta['original_name']
            ?? basename($media->path);

        return $disk->download($media->path, $name, [
            'Access-Control-Allow-Origin' => '*',
        ]);
    }
}
