<?php

namespace App\Http\Controllers\Admin\Book;

use App\Http\Controllers\Controller;
use App\Http\Requests\Book\StoreBookRequest;
use App\Http\Requests\Book\UpdateBookRequest;
use App\Http\Resources\Book\BookResource;
use App\Http\Resources\Book\SuggestedBookResource;
use App\Models\Book\Book;
use App\Models\Book\BookCategory;
use App\Services\Admin\Book\BookService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
        $adminId = $request->user('admin')?->id;

        $book = $this->bookService->create(
            [...$request->validated(), 'uploaded_by' => $adminId]
        );

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

    public function getCategories()
    {
        return response()->json(BookCategory::select('id', 'name')->get());
    }
}
