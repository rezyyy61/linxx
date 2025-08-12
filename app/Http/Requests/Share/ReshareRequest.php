<?php

namespace App\Http\Requests\Share;

use Illuminate\Foundation\Http\FormRequest;

class ReshareRequest extends FormRequest
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
            'share_slug' => ['required','string','max:100'],
            'text' => ['nullable','string','max:90000'],
        ];
    }
}
