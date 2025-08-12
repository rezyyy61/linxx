<?php

namespace App\Http\Requests\Admin\Users;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = (int) $this->route('id');

        return [
            'name'  => ['sometimes','string','max:255'],
            'email' => ['sometimes','email','max:255', Rule::unique('users','email')->ignore($id)],
            'slug'  => ['sometimes','string','max:255', Rule::unique('users','slug')->ignore($id)],
            'is_verified' => ['sometimes','boolean'],
            'verification_expires_at' => ['nullable','date_format:Y-m-d\TH:i'],

            'profile' => ['sometimes','array'],
            'profile.group_name' => ['sometimes','nullable','string','max:255'],
            'profile.tagline' => ['sometimes','nullable','string','max:255'],
            'profile.entity_type' => ['sometimes','in:party,collective,media,individual'],
            'profile.pending_entity_type' => ['sometimes','nullable','in:party,collective,media,individual'],
            'profile.entity_type_approved' => ['sometimes','boolean'],
            'profile.location' => ['sometimes','nullable','string','max:255'],
            'profile.founded_year' => ['sometimes','nullable','string','max:255'],
            'profile.logo_path' => ['sometimes','nullable','string','max:255'],
            'profile.about' => ['sometimes','nullable','string'],
            'profile.goals' => ['sometimes','nullable','string'],
            'profile.activities' => ['sometimes','nullable','string'],
            'profile.structure' => ['sometimes','nullable','string'],
            'profile.avatar_color' => ['sometimes','nullable','string','max:7'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $v = $this->input('verification_expires_at');
        if ($v === '') {
            $this->merge(['verification_expires_at' => null]);
            return;
        }
        if (is_string($v) && preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/', $v)) {
            $dt = Carbon::createFromFormat('Y-m-d\TH:i', $v, config('app.timezone'))->toDateTimeString();
            $this->merge(['verification_expires_at' => $dt]);
        }
    }
}
