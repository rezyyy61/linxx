<?php

namespace App\Http\Controllers\Admin\Book;

use App\Http\Controllers\Controller;
use App\Http\Requests\Book\StoreSuggestionRequest;
use App\Http\Requests\Book\UpdateBookRequest;
use App\Http\Requests\Book\UpdateSuggestionRequest;
use App\Http\Resources\Book\BookResource;
use App\Http\Resources\Book\SuggestedBookResource;
use App\Models\Book\SuggestedBook;
use App\Services\Admin\Book\SuggestedBookService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SuggestedBookController extends Controller
{
    public function __construct(private readonly SuggestedBookService $service)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $status = $request->get('status', 'pending');
        $books  = $this->service->list($status);

        return response()->json(SuggestedBookResource::collection($books));
    }

    public function show(SuggestedBook $suggested): JsonResponse
    {
        return response()->json(new SuggestedBookResource($this->service->show($suggested)));
    }

    public function update(UpdateBookRequest $request, SuggestedBook $suggested): JsonResponse
    {
        $updated = $this->service->update($suggested, $request->validated());

        return response()->json(new SuggestedBookResource($updated));
    }

    public function approve(
        SuggestedBook     $suggested,
        UpdateBookRequest $request
    ): JsonResponse {

        $adminId = $request->user('admin')?->id;

        $extra = [
            'uploaded_by' => $adminId,
            'category_id' => $request->validated('category_id'),
        ];

        if ($request->hasFile('cover_image')) {
            $extra['new_cover_tmp'] = $request->file('cover_image')
                ->store('books/tmp', 'public');
        }
        if ($request->hasFile('file_path')) {
            $extra['new_file_tmp'] = $request->file('file_path')
                ->store('books/tmp', 'public');
        }

        $book = $this->service->approve($suggested, $extra);

        return response()->json([
            'message' => 'Suggestion approved and published.',
            'book'    => new BookResource($book),
        ], 201);
    }

    public function destroy(SuggestedBook $suggested): JsonResponse
    {
        $this->service->delete($suggested);

        return response()->json(['message' => 'Suggestion deleted.']);
    }
}
