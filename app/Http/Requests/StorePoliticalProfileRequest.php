<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePoliticalProfileRequest extends FormRequest
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
            'group_name'    => 'required|string|max:255',
            'tagline'       => 'nullable|string|max:255',
            'entity_type'   => 'required|in:party,collective,media,individual',
            'location'      => 'nullable|string|max:255',
            'founded_year'  => 'nullable|integer|min:1800|max:' . date('Y'),
            'about'         => 'nullable|string',
            'goals'         => 'nullable|string',
            'activities'    => 'nullable|string',
            'structure'     => 'nullable|string',

            'links'         => 'nullable|array',
            'ideologies'    => 'nullable|array',
            'files'         => 'nullable|array',
        ];
    }
}
