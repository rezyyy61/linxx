<?php

namespace App\Actions;

use App\Services\FileSummarizer;
use App\Services\FileUploader;
use Illuminate\Http\UploadedFile;

class SummarizeFile
{
    public function __construct(
        private FileUploader    $uploader,
        private FileSummarizer $summarizer,
    ) {}

    public function handle(UploadedFile $file): string
    {
        $path = $this->uploader->upload($file, 'publications');
        $abs  = storage_path('app/public/'.$path);

        // گرفتن خلاصه
        $summary = $this->summarizer->summarize($abs);

        // پاک‌سازی فایل موقت
        unlink($abs);

        return $summary;
    }
}
