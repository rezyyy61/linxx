<?php

namespace App\Http\Requests\Event\Public;

use Illuminate\Foundation\Http\FormRequest;

class EventPublicIndexRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'q' => ['nullable','string','max:200'],
            'city' => ['nullable','string','max:120'],
            'type' => ['nullable','string','max:80'],
            'mode' => ['nullable','in:offline,online,hybrid'],
            'creator_id' => ['nullable','integer'],
            'from' => ['nullable','date'],
            'to' => ['nullable','date','after_or_equal:from'],
            'upcoming' => ['nullable','boolean'],
            'sort' => ['nullable','in:soon,latest'],
            'per_page' => ['nullable','integer','min:1','max:50'],
        ];
    }
}
