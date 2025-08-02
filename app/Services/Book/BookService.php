<?php

namespace App\Services\Book;

use App\Models\Book;
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
        return Book::create($data);
    }

    public function show(Book $book): Book
    {
        return $book->load(['category', 'uploader']);
    }

    public function update(Book $book, array $data): Book
    {
        if (isset($data['title'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        $book->update($data);
        return $book;
    }

    public function delete(Book $book): bool
    {
        return $book->delete();
    }
}
