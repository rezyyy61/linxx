<?php

namespace App\Http\Requests\Admin\Users;

use Illuminate\Foundation\Http\FormRequest;

class UserIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'q' => ['nullable','string','max:120'],
            'entity_type' => ['nullable','in:party,collective,media,individual'],
            'per_page' => ['nullable','integer','min:1','max:200'],
        ];
    }
}
