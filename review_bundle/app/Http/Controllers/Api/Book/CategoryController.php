<?php

namespace App\Http\Controllers\Api\Book;

use App\Http\Controllers\Controller;
use App\Models\Book\BookCategory;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    public function index(): JsonResponse
    {
        $categories = BookCategory::select('id', 'name')->orderBy('name')->get();
        return response()->json([
            'data' => $categories
        ]);
    }
}
