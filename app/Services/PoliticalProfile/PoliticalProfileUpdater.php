<?php

namespace App\Services\PoliticalProfile;

use App\Mail\EntityTypeApprovalRequested;
use App\Models\PoliticalProfile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class PoliticalProfileUpdater
{
    public function updateWithRelations(array $data, PoliticalProfile $profile): PoliticalProfile
    {
        return DB::transaction(function () use ($data, $profile) {
            $fillableFields = $profile->getFillable();
            $toUpdate = [];
            $requestedTypeForEmail = null;

            foreach ($fillableFields as $field) {
                if (!array_key_exists($field, $data)) continue;

                // Handle entity_type approval logic
                if ($field === 'entity_type') {
                    $newType = $data[$field];
                    $currentType = $profile->entity_type;

                    if ($newType !== $currentType && $newType !== 'individual') {
                        $profile->pending_entity_type = $newType;
                        $profile->entity_type_approved = false;
                        $requestedTypeForEmail = $newType;
                    } elseif ($newType === 'individual') {
                        $toUpdate[$field] = $newType;
                        $profile->pending_entity_type = null;
                        $profile->entity_type_approved = true;
                    }

                    continue;
                }

                $toUpdate[$field] = $data[$field];
            }

            $profile->fill($toUpdate);
            $profile->save();

            $this->handleLogo($profile);
            $this->handleLinks($profile, $data);
            $this->handleIdeologies($profile, $data['ideologies'] ?? []);
            $this->handleFiles($profile, $data);

            if ($requestedTypeForEmail && $profile->user?->email) {
                DB::afterCommit(function () use ($profile, $requestedTypeForEmail) {
                    Mail::to($profile->user->email)->queue(new EntityTypeApprovalRequested($profile->fresh('user'), $requestedTypeForEmail));
                });
            }

            return $profile->load(['links', 'ideologies', 'files']);
        });
    }
    protected function handleLogo(PoliticalProfile $profile): void
    {
        if (request()->has('logo_removed') && request('logo_removed') === '1') {
            if ($profile->logo_path) {
                Storage::disk('public')->delete($profile->logo_path);
            }

            $profile->update(['logo_path' => null]);
            return;
        }

        if (request()->hasFile('logo')) {
            if ($profile->logo_path) {
                Storage::disk('public')->delete($profile->logo_path);
            }

            $logoPath = request()->file('logo')->store('profiles/logos', 'public');
            $profile->update(['logo_path' => $logoPath]);
        }
    }
    protected function handleLinks(PoliticalProfile $profile, array $data): void
    {
        $links = collect($data['links'] ?? []);
        $removed = $data['removed_links'] ?? [];

        $keepIds = [];

        foreach ($links as $link) {
            if (!empty($link['id']) && in_array($link['id'], $removed)) continue;

            $values = [
                'type'  => $link['type'] ?? 'custom',
                'title' => $link['title'] ?? null,
                'url'   => $link['url'] ?? null,
            ];

            if (!empty($link['id'])) {
                $profile->links()->where('id', $link['id'])->update($values);
                $keepIds[] = $link['id'];
            } else {
                $created = $profile->links()->create($values);
                $keepIds[] = $created->id;
            }
        }

        if (!empty($removed)) {
            $profile->links()->whereIn('id', $removed)->delete();
        }

        $profile->links()->whereNotIn('id', $keepIds)->delete();
    }

    protected function handleIdeologies(PoliticalProfile $profile, array $ideologies): void
    {
        $existingIds = [];
        $newValues = [];

        foreach ($ideologies as $item) {
            $values = [
                'value' => $item['value'],
                'type'  => $item['type'] ?? 'custom',
            ];

            if (!empty($item['id'])) {
                // Update existing one
                $profile->ideologies()->where('id', $item['id'])->update($values);
                $existingIds[] = $item['id'];
            } else {
                // Avoid duplicate creation
                $newValues[] = $values;
            }
        }

        // Delete removed ideologies (those not in input anymore)
        $profile->ideologies()
            ->whereNotIn('id', $existingIds)
            ->delete();

        // Create new unique ideologies (avoid duplicates)
        foreach ($newValues as $values) {
            $exists = $profile->ideologies()
                ->where('value', $values['value'])
                ->where('type', $values['type'])
                ->exists();

            if (!$exists) {
                $profile->ideologies()->create($values);
            }
        }
    }

    protected function handleFiles(PoliticalProfile $profile, array $data): void
    {
        $removed = $data['removed_files'] ?? [];

        if (!empty($removed)) {
            $profile->files()->whereIn('id', $removed)->each(function ($file) {
                Storage::disk('public')->delete($file->file_path);
                $file->delete();
            });
        }

        $files = request('files', []);

        foreach ($files as $item) {
            if (!isset($item['file'], $item['section']) || isset($item['id'])) continue;

            $file = $item['file'];
            $section = $item['section'];

            if (!is_file($file)) continue;

            $path = $file->store("profiles/files/{$profile->id}/{$section}", 'public');

            $profile->files()->create([
                'section'    => $section,
                'file_path'  => $path,
                'file_name'  => $file->getClientOriginalName(),
                'mime_type'  => $file->getClientMimeType(),
                'file_size'  => $file->getSize(),
            ]);
        }
    }
}