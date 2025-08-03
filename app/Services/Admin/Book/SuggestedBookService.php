<?php

namespace App\Services\Admin\Book;

use App\Models\Book\Book;
use App\Models\Book\SuggestedBook;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SuggestedBookService
{
    public function list(string $status = 'pending', int $perPage = 15): LengthAwarePaginator
    {
        return SuggestedBook::with('submitter:id,name,email')
            ->where('status', $status)
            ->latest()
            ->paginate($perPage);
    }

    public function show(SuggestedBook $suggested): SuggestedBook
    {
        return $suggested->load('submitter:id,name,email');
    }

    public function update(SuggestedBook $suggested, array $data): SuggestedBook
    {
        $suggested->fill($data)->save();
        return $suggested->fresh();
    }

    public function delete(SuggestedBook $suggested): bool
    {
        if ($suggested->cover_path) {
            Storage::disk('public')->delete($suggested->cover_path);
        }
        if ($suggested->file_path) {
            Storage::disk('public')->delete($suggested->file_path);
        }

        return $suggested->delete();
    }

    public function approve(SuggestedBook $suggested, array $extra = []): Book
    {
        return DB::transaction(function () use ($suggested, $extra) {

            $tmpCover = $extra['new_cover_tmp'] ?? $suggested->cover_path;
            $tmpFile  = $extra['new_file_tmp']  ?? $suggested->file_path;

            if (!$tmpFile) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'file_path' => ['Book file is required.'],
                ]);
            }

            $book = Book::create([
                'title'       => $suggested->title,
                'slug'        => $this->uniqueSlug($suggested->title),
                'author'      => $suggested->author,
                'translator'  => $suggested->translator,
                'description' => $suggested->description,
                'cover_image' => '',
                'file_path'   => '',
                'uploaded_by' => $extra['uploaded_by'] ?? $suggested->submitted_by,
                'category_id' => $extra['category_id'] ?? null,
            ]);

            if ($tmpCover) {
                $newCover = "books/{$book->id}/cover/" . basename($tmpCover);
                Storage::disk('public')->move($tmpCover, $newCover);
                $book->update(['cover_image' => $newCover]);
            }

            $newFile = "books/{$book->id}/file/" . basename($tmpFile);
            Storage::disk('public')->move($tmpFile, $newFile);
            $book->update(['file_path' => $newFile]);

            $suggested->delete();

            return $book->fresh();
        });
    }



    protected function uniqueSlug(string $title): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $i    = 2;

        while (Book::where('slug', $slug)->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }
}
