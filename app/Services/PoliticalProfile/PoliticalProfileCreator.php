<?php

namespace App\Services\PoliticalProfile;

use App\Models\PoliticalProfile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PoliticalProfileCreator
{
    public function createWithRelations(array $data, int $userId): PoliticalProfile
    {
        return DB::transaction(function () use ($data, $userId) {
            $profile = PoliticalProfile::create(array_merge(
                array_intersect_key($data, array_flip((new PoliticalProfile)->getFillable())),
                ['user_id' => $userId]
            ));

            $this->handleLogo($profile);
            $this->handleLinks($profile, $data['links'] ?? []);
            $this->handleIdeologies($profile, $data['ideologies'] ?? []);
            $this->handleFiles($profile, request('files', []));

            return $profile->load(['links', 'ideologies', 'files']);
        });
    }

    protected function handleLogo(PoliticalProfile $profile): void
    {
        if (request()->hasFile('logo')) {
            $logoPath = request()->file('logo')->store('profiles/logos', 'public');
            $profile->update(['logo_path' => $logoPath]);
        }
    }

    protected function handleLinks(PoliticalProfile $profile, array $links): void
    {
        $links = collect($links)
            ->map(fn($item) => is_string($item) ? json_decode($item, true) : $item)
            ->filter()
            ->map(fn($item) => [
                'type'  => $item['type'] ?? 'custom',
                'title' => $item['title'] ?? null,
                'url'   => $item['url'] ?? null,
            ]);

        $profile->links()->createMany($links->toArray());
    }

    protected function handleIdeologies(PoliticalProfile $profile, array $ideologies): void
    {
        $ideologies = collect($ideologies)
            ->map(fn($item) => [
                'value' => is_array($item) ? $item['value'] : $item,
                'type'  => is_array($item) && isset($item['type']) ? $item['type'] : 'custom'
            ]);

        $profile->ideologies()->createMany($ideologies->toArray());
    }

    protected function handleFiles(PoliticalProfile $profile, array $files): void
    {
        foreach ($files as $item) {
            if (!isset($item['file'], $item['section'])) continue;

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