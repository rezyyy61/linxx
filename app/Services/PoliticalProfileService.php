<?php

namespace App\Services;

use App\Models\PoliticalProfile;
use Illuminate\Support\Facades\DB;

class PoliticalProfileService
{

    public function storeOrUpdate(array $data, int $userId): PoliticalProfile
    {
        $existing = PoliticalProfile::where('user_id', $userId)->first();

        if ($existing) {
            return $this->updateWithRelations($data, $existing);
        }

        return $this->createWithRelations($data, $userId);
    }

    public function createWithRelations(array $data, int $userId): PoliticalProfile
    {
        return DB::transaction(function () use ($data, $userId) {
            $profile = PoliticalProfile::create([
                ...$data,
                'user_id' => $userId,
            ]);

            // ذخیره لوگو
            if (request()->hasFile('logo')) {
                $logoPath = request()->file('logo')->store('profiles/logos', 'public');
                $profile->update(['logo_path' => $logoPath]);
            }

            $links = collect($data['links'] ?? [])
                ->map(fn($item) => is_string($item) ? json_decode($item, true) : $item)
                ->filter();

            $profile->links()->createMany($links->toArray());

            // ایدئولوژی‌ها
            foreach ($data['ideologies'] ?? [] as $item) {
                $profile->ideologies()->create([
                    'value' => is_array($item) ? $item['value'] : $item,
                    'type'  => is_array($item) && isset($item['type']) ? $item['type'] : 'custom',
                ]);
            }

            // فایل‌ها
            foreach (request('files', []) as $index => $item) {
                if (!isset($item['file']) || !isset($item['section'])) continue;

                $file    = $item['file'];
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

            return $profile->load(['links', 'ideologies', 'files']);
        });
    }

    public function updateWithRelations(array $data, PoliticalProfile $profile): PoliticalProfile
    {
        return DB::transaction(function () use ($data, $profile) {
            $profile->update($data);

            // لوگو جدید
            if (request()->hasFile('logo')) {
                $logoPath = request()->file('logo')->store('profiles/logos', 'public');
                $profile->update(['logo_path' => $logoPath]);
            }

            // لینک‌ها
            $profile->links()->delete();
            $links = collect($data['links'] ?? [])
                ->map(fn($item) => is_string($item) ? json_decode($item, true) : $item)
                ->filter();

            $profile->links()->createMany($links->toArray());

            // ایدئولوژی‌ها
            $profile->ideologies()->delete();
            foreach ($data['ideologies'] ?? [] as $item) {
                $profile->ideologies()->create([
                    'value' => is_array($item) ? $item['value'] : $item,
                    'type'  => is_array($item) && isset($item['type']) ? $item['type'] : 'custom',
                ]);
            }

            // فایل‌ها
            $profile->files()->delete();
            foreach (request('files', []) as $item) {
                if (!isset($item['file']) || !isset($item['section'])) continue;

                $file    = $item['file'];
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

            return $profile->load(['links', 'ideologies', 'files']);
        });
    }

    public function getForUser(int $userId): ?PoliticalProfile
    {
        if (!$userId) {
            return null;
        }

        return PoliticalProfile::with(['links', 'ideologies', 'files'])
            ->where('user_id', $userId)
            ->first();
    }

}
