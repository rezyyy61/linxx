<?php

namespace App\Http\Requests\Event;

use Illuminate\Foundation\Http\FormRequest;

class EventInviteRespondRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required','string','max:24'], // accepted|declined|pending
            'token'  => ['nullable','string','max:191'],
        ];
    }
}
