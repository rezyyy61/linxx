<?php

namespace App\Http\Controllers\Api\Book;

use App\Http\Controllers\Controller;
use App\Http\Resources\Book\BookResource;
use App\Http\Resources\Book\ReviewResource;
use App\Models\Book\Book;
use App\Models\Book\BookReview;
use App\Services\Book\PublicBookService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PublicBookController extends Controller
{
    protected PublicBookService $bookService;

    public function __construct(PublicBookService $bookService)
    {
        $this->bookService = $bookService;
    }

    public function index(): JsonResponse
    {
        $books = $this->bookService->paginate();
        return BookResource::collection($books)->response();
    }

    public function show(string $slug): JsonResponse
    {
        $book = Book::with(['category'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->where('slug', $slug)
            ->firstOrFail();

        $book->increment('view_count');

        return response()->json(new BookResource($book));
    }


    public function download(string $slug)
    {
        $book = Book::where('slug', $slug)->firstOrFail();

        if (!$book->file_path || !Storage::disk('public')->exists($book->file_path)) {
            abort(404);
        }

        $book->increment('download_count');

        return Storage::disk('public')->download($book->file_path, $book->title . '.pdf');
    }


    public function submitReview(Request $request, Book $book)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:2000',
        ]);

        $review = BookReview::updateOrCreate(
            ['book_id' => $book->id, 'user_id' => auth()->id()],
            ['rating' => $validated['rating'], 'review' => $validated['review']]
        );

        return response()->json(['message' => 'Review submitted successfully.'], 201);
    }

    public function reviews(Book $book)
    {
        $reviews = $book->reviews()->latest()->with('user')->get();
        return ReviewResource::collection($reviews);
    }

    public function stream(Book $book)
    {
        $path = storage_path("app/public/{$book->file_path}");

        if (!Storage::disk('public')->exists($book->file_path)) {
            abort(404);
        }

        return response()->file($path, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$book->title.'.pdf"',
            'Access-Control-Allow-Origin' => '*',
        ]);

    }

    public function search(Request $request): JsonResponse
    {
        $books = $this->bookService->search($request->all());
        return BookResource::collection($books)->response();
    }

}
