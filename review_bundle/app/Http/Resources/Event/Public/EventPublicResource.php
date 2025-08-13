<?php

namespace App\Http\Resources\Event\Public;

use Illuminate\Http\Resources\Json\JsonResource;

class EventPublicResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'              => $this->id,
            'slug'            => $this->slug,
            'title'           => $this->title,
            'description'     => $this->description,
            'type'            => $this->type,
            'type_custom'     => $this->type_custom,
            'mode'            => $this->mode,
            'visibility'      => $this->visibility,
            'status'          => $this->status,
            'timezone'        => $this->timezone,
            'starts_at'       => optional($this->starts_at)->toIso8601String(),
            'ends_at'         => optional($this->ends_at)->toIso8601String(),
            'city'            => $this->city,
            'venue_name'      => $this->venue_name,
            'address'         => $this->address,
            'country'         => $this->country,
            'platform'        => $this->platform,
            'stream_url'      => $this->stream_url,
            'meeting_link'    => $this->meeting_link,
            'access_code'     => $this->access_code,
            'cover_url'       => $this->cover_url,
            'attachment_url'  => $this->attachment_url,
            'attachment_name' => $this->attachment_path ? basename($this->attachment_path) : null,

            'creator' => [
                'type'         => class_basename($this->creator_type),
                ...($this->relationLoaded('creator') && $this->creator ? [
                    'id'           => $this->creator->id,
                    'name'         => $this->creator->name ?? $this->creator->title ?? null,
                    'slug'         => $this->creator->slug ?? null,
                    'avatar_color' => optional($this->creator->politicalProfile)->avatar_color
                        ?? ($this->creator->color ?? null),
                    'logo_url'     => optional($this->creator->politicalProfile)->logo_url
                        ?? ($this->creator->logo_url ?? null),
                ] : []),
            ],

            'created_by' => $this->whenLoaded('createdBy', function () {
                return [
                    'id'           => $this->createdBy->id,
                    'name'         => $this->createdBy->name,
                    'slug'         => $this->createdBy->slug,
                    'avatar_color' => optional($this->createdBy->politicalProfile)->avatar_color,
                    'logo_url'     => optional($this->createdBy->politicalProfile)->logo_url,
                ];
            }),

            'rsvp' => [
                'enabled'   => $this->isRsvpEnabled(),
                'capacity'  => $this->rsvp_capacity ?? null,
                'ends_at'   => optional($this->rsvp_ends_at)->toIso8601String(),
                'my_status' => $this->myRsvpStatus($request),
                'counts'    => $this->rsvpCounts(),
            ],
        ];
    }

    protected function isRsvpEnabled(): bool
    {
        $now = now();
        $s = $this->starts_at;
        $e = $this->ends_at;
        $upcoming = $s && $s->isFuture();
        $live = $s && $s->lte($now) && (!$e || $e->gte($now));
        $deadline = $this->rsvp_ends_at ?: $this->starts_at;
        $deadlineOk = $deadline ? $deadline->gt($now) : true;
        return ($upcoming || $live) && $deadlineOk && $this->status === 'published' && $this->visibility === 'public';
    }

    protected function myRsvpStatus($request): ?string
    {
        $user = $request->user();
        if (!$user) return null;
        $row = $this->rsvps()->select('status')->where('user_id', $user->id)->first();
        return $row?->status;
    }

    protected function rsvpCounts(): ?array
    {
        if (isset($this->rsvp_going_count) || isset($this->rsvp_interested_count)) {
            return [
                'going'      => (int) ($this->rsvp_going_count ?? 0),
                'interested' => (int) ($this->rsvp_interested_count ?? 0),
            ];
        }
        if ($this->relationLoaded('rsvps')) {
            $going = $this->rsvps->where('status', 'going')->count();
            $interested = $this->rsvps->where('status', 'interested')->count();
            return ['going' => $going, 'interested' => $interested];
        }
        return null;
    }
}