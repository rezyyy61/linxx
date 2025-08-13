<?php

namespace App\Http\Requests\Book;

use Illuminate\Foundation\Http\FormRequest;

class StoreSuggestionRequest extends FormRequest
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
            'title'       => 'required|string|max:255',
            'author'      => 'nullable|string|max:255',
            'translator'  => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'link'        => 'nullable|url|max:255',
            'cover_path'       => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
            'file_path'        => 'nullable|file|mimes:pdf|max:51200',
        ];
    }

}
