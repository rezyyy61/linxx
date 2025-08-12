<?php

namespace App\Http\Requests\Share;

use Illuminate\Foundation\Http\FormRequest;

class StoreShareRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'shareable_type' => ['required','string'],
            'shareable_id' => ['required','integer'],
            'scope' => ['required','in:public,friends,private,link'],
            'expires_at' => ['nullable','date'],
            'max_clicks' => ['nullable','integer','min:1'],
            'allow_repost' => ['nullable','boolean'],
            'password' => ['nullable','string','min:4'],
            'extra' => ['nullable','array'],
        ];
    }
}
