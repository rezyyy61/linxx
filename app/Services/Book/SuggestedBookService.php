<?php

namespace App\Services\Book;

use App\Models\Book\SuggestedBook;
use App\Services\Media\FileScanner;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Exception;

class SuggestedBookService
{
    public function __construct(
        protected FileScanner $scanner
    ) {}

    /**
     * Store a new suggested book
     */
    public function store(array $data, ?UploadedFile $cover = null, ?UploadedFile $file = null): SuggestedBook
    {
        $paths = [
            'cover_path' => null,
            'file_path' => null,
        ];

        // Upload and scan files
        if ($cover) {
            $paths['cover_path'] = $this->storeAndScanFile($cover, 'suggested_books/covers');
        }

        if ($file) {
            $paths['file_path'] = $this->storeAndScanFile($file, 'suggested_books/files');
        }

        return SuggestedBook::create([
            'title' => $data['title'],
            'author' => $data['author'] ?? null,
            'translator' => $data['translator'] ?? null,
            'description' => $data['description'] ?? null,
            'link' => $data['link'] ?? null,
            'submitted_by' => auth()->id(),
            'cover_path' => $paths['cover_path'],
            'file_path' => $paths['file_path'],
        ]);
    }

    private function storeAndScanFile(UploadedFile $file, string $dir): string
    {
        $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs($dir, $filename, 'public');

        $absolutePath = Storage::disk('public')->path($path);
        $this->scanner->isClean($absolutePath);

        return $path;
    }
}
