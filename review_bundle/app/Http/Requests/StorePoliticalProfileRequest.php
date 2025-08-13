<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePoliticalProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        foreach ([
                     'ideologies',
                     'links',
                     'files',
                     'removed_files',
                     'removed_links'
                 ] as $field) {
            if ($this->has($field) && is_string($this->{$field})) {
                $decoded = json_decode($this->{$field}, true);
                $this->merge([
                    $field => is_array($decoded) ? $decoded : []
                ]);
            }
        }

        // Normalize empty strings to null for all relevant fields
        foreach ([
                     'group_name', 'tagline', 'entity_type', 'location', 'founded_year',
                     'about', 'goals', 'activities', 'structure', 'avatar_color'
                 ] as $textField) {
            if ($this->has($textField) && trim($this->{$textField}) === '') {
                $this->merge([
                    $textField => null
                ]);
            }
        }
    }

    public function rules(): array
    {
        $isUpdate = $this->user()?->politicalProfile()->exists();

        return [
            'group_name'   => [$isUpdate ? 'sometimes' : 'required', 'string', 'max:255'],
            'tagline'      => ['nullable', 'string', 'max:255'],
            'entity_type'  => [$isUpdate ? 'sometimes' : 'required', Rule::in(['party', 'collective', 'media', 'individual'])],
            'location'     => ['nullable', 'string', 'max:255'],
            'founded_year' => ['nullable', 'string'],
            'about'        => ['nullable', 'string', 'max:5000'],
            'goals'        => ['nullable', 'string', 'max:5000'],
            'activities'   => ['nullable', 'string', 'max:5000'],
            'structure'    => ['nullable', 'string', 'max:5000'],
            'avatar_color' => ['nullable', 'string', 'size:7'],
            'logo'    => ['nullable', 'image', 'max:2048'],

            'links'                => ['nullable', 'array'],
            'links.*.id'           => ['nullable', 'integer', 'exists:profile_links,id'],
            'links.*.type'         => ['required_with:links', 'string', Rule::in(['website', 'youtube', 'email', 'phone', 'telegram', 'instagram', 'facebook', 'twitter', 'custom'])],
            'links.*.title'        => ['nullable', 'string', 'max:255'],
            'links.*.url'          => ['required_with:links', 'string', 'max:255'],

            'ideologies'           => ['nullable', 'array'],
            'ideologies.*.id'      => ['nullable', 'integer', 'exists:profile_ideologies,id'],
            'ideologies.*.value'   => ['required_with:ideologies', 'string', 'max:255'],
            'ideologies.*.type'    => ['nullable', Rule::in(['predefined', 'custom'])],

            'files'                => ['nullable', 'array', 'max:10'],
            'files.*.id'           => ['nullable', 'integer', 'exists:profile_files,id'],
            'files.*.section'      => ['required_without:files.*.id', 'string', Rule::in(['about', 'goals', 'activities', 'structure'])],
            'files.*.file'         => ['required_without:files.*.id', 'file', 'max:5120'],

            'removed_files'        => ['nullable', 'array'],
            'removed_files.*'      => ['integer', 'exists:profile_files,id'],

            'removed_links'        => ['nullable', 'array'],
            'removed_links.*'      => ['integer', 'exists:profile_links,id'],
        ];
    }
}