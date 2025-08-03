<?php

namespace App\Services\Admin\Book;

use App\Models\Book\Book;
use App\Models\Book\SuggestedBook;
use Illuminate\Support\Str;

class BookService
{
    public function list(int $perPage = 10)
    {
        return Book::with(['category', 'uploader'])->latest()->paginate($perPage);
    }

    public function create(array $data): Book
    {
        $data['slug'] = Str::slug($data['title']);
        $book = Book::create($data);

        $updatedData = [];

        if (request()->hasFile('cover_image')) {
            $coverPath = request()->file('cover_image')->store("books/{$book->id}/cover", 'public');
            $updatedData['cover_image'] = $coverPath;
        }

        if (request()->hasFile('file_path')) {
            $filePath = request()->file('file_path')->store("books/{$book->id}/file", 'public');
            $updatedData['file_path'] = $filePath;
        }

        if (!empty($updatedData)) {
            $book->update($updatedData);
        }

        return $book;
    }


    public function show(Book $book): Book
    {
        return $book->load(['category', 'uploader']);
    }

    public function update(Book $book, array $data): Book
    {
        $book->fill($data);

        if (request()->hasFile('cover_image')) {
            $path = request()->file('cover_image')
                ->store("books/{$book->id}/cover", 'public');
            $book->cover_image = $path;
        }

        if (request()->hasFile('file_path')) {
            $path = request()->file('file_path')
                ->store("books/{$book->id}/file", 'public');
            $book->file_path = $path;
        }

        $book->save();

        return $book->fresh();
    }

    public function delete(Book $book): bool
    {
        return $book->delete();
    }

}
