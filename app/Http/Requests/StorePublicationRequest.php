<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePublicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'        => ['required', 'string', 'max:255'],
            'issue'        => ['required', 'nullable', 'string', 'max:50'],
            'description'  => ['required', 'nullable', 'string'],
            'published_at' => ['required', 'nullable', 'date'],
            'file'         => [
                'required',
                'file',
                'mimes:pdf,doc,docx',
                'max:20480',   // 20 MB
            ],
        ];
    }
}
