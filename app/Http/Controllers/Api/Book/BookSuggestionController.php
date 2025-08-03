<?php

namespace App\Http\Controllers\Api\Book;

use App\Http\Controllers\Controller;
use App\Http\Requests\Book\StoreSuggestionRequest;
use App\Services\Book\SuggestedBookService;
use Illuminate\Http\JsonResponse;

class BookSuggestionController extends Controller
{
    public function __construct(
        protected SuggestedBookService $service
    ) {}

    /**
     * Handle storing a user-suggested book
     */
    public function store(StoreSuggestionRequest $request): JsonResponse
    {
        $data = $request->validated();

        $cover = $request->file('cover_path');
        $file = $request->file('file_path');

        $this->service->store($data, $cover, $file);

        return response()->json([
            'message' => __('Book suggestion submitted successfully.')
        ], 201);
    }
}