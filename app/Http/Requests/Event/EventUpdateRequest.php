<?php

namespace App\Http\Requests\Event;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EventUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $eventId = $this->route('event')?->id ?? $this->route('id');

        return [
            'title'             => ['required','string','max:180'],
            'slug'              => ['nullable','string','max:191','unique:events,slug'],
            'description'       => ['nullable','string'],

            'type'              => ['required','in:rally_protest,town_hall,meeting_internal,training,webinar,fundraiser,custom'],
            'type_custom'       => ['nullable','string','max:80','required_if:type,custom'],
            'mode'              => ['nullable','string','max:32'],
            'visibility'        => ['nullable','string','max:16'],
            'allow_comments'    => ['boolean'],

            'timezone'          => ['required','string','max:64'],
            'starts_at'         => ['required','date'],
            'ends_at'           => ['nullable','date','after:starts_at'],
            'rsvp_deadline'     => ['nullable','date','before_or_equal:starts_at'],

            'capacity'          => ['nullable','integer','min:1'],
            'requires_approval' => ['boolean'],

            'venue_name'        => ['nullable','string','max:191'],
            'address'           => ['nullable','string','max:255'],
            'city'              => ['nullable','string','max:120'],
            'platform'          => ['nullable','string','max:80'],
            'meeting_link'      => ['nullable','url','max:512'],
            'access_code'       => ['nullable','string','max:128'],
            'country'           => ['nullable','string','max:120'],
            'lat'               => ['nullable','numeric','between:-90,90'],
            'lng'               => ['nullable','numeric','between:-180,180'],

            'cover'            => ['nullable','image','max:5120'],
            'attachment'       => ['nullable','file','max:20480'],
        ];
    }
}
