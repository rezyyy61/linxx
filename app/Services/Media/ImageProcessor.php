<?php

namespace App\Services\Media;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;
use Spatie\LaravelImageOptimizer\Facades\ImageOptimizer;

class ImageProcessor
{
    /**
     * @param \SplFileInfo|string $file مسیر کامل فایل یا آبجکت فایل
     * @param int $postId
     * @return array
     */
    public function process(\SplFileInfo|string $file, int $postId): array
    {
        $disk = 'public';
        $filename = Str::uuid() . '.webp';
        $path = "posts/{$postId}/{$filename}";

        try {
            // اینجا کلید حل مشکله: بررسی می‌کنیم فایل مسیر متنی داره یا آبجکت
            $image = is_string($file)
                ? Image::read($file)
                : Image::read($file->getRealPath());

            $image = $image->resize(1920, 1920, fn ($c) => $c->aspectRatio()->upsize());

            $width = $image->width();
            $height = $image->height();

            $encoded = $image->toWebp(80);

            Storage::disk($disk)->put($path, (string) $encoded);

            ImageOptimizer::optimize(Storage::disk($disk)->path($path));

            return [
                'path'   => $path,
                'width'  => $width,
                'height' => $height,
                'size'   => Storage::disk($disk)->size($path),
            ];
        } catch (\Throwable $e) {
            report($e);
            Log::error($e);
            throw new \RuntimeException('Image processing failed.');
        }
    }
}
