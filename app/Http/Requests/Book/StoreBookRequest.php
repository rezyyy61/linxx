<?php

namespace App\Http\Requests\Book;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title'        => 'required|string|max:255',
            'author'       => 'nullable|string|max:255',
            'description'  => 'nullable|string',
            'cover_image'  => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
            'file_path'    => 'required|file|mimes:pdf,epub|max:51200',
            'is_free'      => 'required|boolean',
            'category_id'  => 'nullable|exists:book_categories,id',
            'uploaded_by'  => 'required|exists:users,id',
        ];
    }
}
