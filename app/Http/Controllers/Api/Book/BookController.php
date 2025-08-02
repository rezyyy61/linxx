<?php

namespace App\Http\Controllers\Api\Book;

use App\Http\Controllers\Controller;
use App\Http\Requests\Book\StoreBookRequest;
use App\Http\Requests\Book\UpdateBookRequest;
use App\Http\Resources\Book\BookResource;
use App\Models\Book;
use App\Services\Book\BookService;
use Illuminate\Http\JsonResponse;

class BookController extends Controller
{
    protected BookService $bookService;

    public function __construct(BookService $bookService)
    {
        $this->bookService = $bookService;
    }

    public function index(): JsonResponse
    {
        $books = $this->bookService->list();
        return response()->json(BookResource::collection($books));
    }

    public function store(StoreBookRequest $request): JsonResponse
    {
        $book = $this->bookService->create($request->validated());
        return response()->json(new BookResource($book), 201);
    }

    public function show(Book $book): JsonResponse
    {
        $book = $this->bookService->show($book);
        return response()->json(new BookResource($book));
    }

    public function update(UpdateBookRequest $request, Book $book): JsonResponse
    {
        $book = $this->bookService->update($book, $request->validated());
        return response()->json(new BookResource($book));
    }

    public function destroy(Book $book): JsonResponse
    {
        $this->bookService->delete($book);
        return response()->json(['message' => 'Deleted book successfully.']);
    }
}
