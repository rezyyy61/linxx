<?php

namespace App\Services\Event;

use App\Models\Event\Event;
use App\Services\Event\Geo\Geocoder;
use App\Services\Media\FileScanner;
use App\Services\Media\ImageProcessor;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EventService
{
    public function __construct(
        private FileScanner $scanner,
        private ImageProcessor $images,
        private Geocoder $geocoder
    ) {}

    public function create(array $data, ?UploadedFile $cover = null, ?UploadedFile $attachment = null): Event
    {
        $data['slug'] = $data['slug'] ?? Str::slug($data['title']) . '-' . Str::random(6);

        return DB::transaction(function () use ($data, $cover, $attachment) {
            $event = Event::create($data);
            $this->maybeGeocode($event, $data);

            if ($cover instanceof UploadedFile) {
                $this->scanner->isClean($cover->getRealPath());
                $coverInfo = $this->images->process($cover, $event->id);
                $event->cover_path = $this->relocateCoverToEvents($event, $coverInfo['path']);
            }

            if ($attachment instanceof UploadedFile) {
                $this->scanner->isClean($attachment->getRealPath());
                $event->attachment_path = $this->storeAttachment($event, $attachment);
            }

            $event->save();

            return $event;
        });
    }

    public function update(Event $event, array $data, ?UploadedFile $cover = null, ?UploadedFile $attachment = null): Event
    {
        return DB::transaction(function () use ($event, $data, $cover, $attachment) {
            $event->update($data);
            $this->maybeGeocode($event, $data);

            if ($cover instanceof UploadedFile) {
                $this->scanner->isClean($cover->getRealPath());
                $this->deleteIfExists($event->cover_path);
                $coverInfo = $this->images->process($cover, $event->id);
                $event->cover_path = $this->relocateCoverToEvents($event, $coverInfo['path']);
            }

            if ($attachment instanceof UploadedFile) {
                $this->scanner->isClean($attachment->getRealPath());
                $this->deleteIfExists($event->attachment_path);
                $event->attachment_path = $this->storeAttachment($event, $attachment);
            }

            $event->save();

            return $event;
        });
    }

    public function delete(Event $event): bool
    {
        $this->deleteIfExists($event->cover_path);
        $this->deleteIfExists($event->attachment_path);
        return $event->delete();
    }

    public function getUpcoming(int $limit = 10)
    {
        return Event::upcoming()->limit($limit)->get();
    }

    public function getByCreator($creatorType, $creatorId)
    {
        return Event::where('creator_type', $creatorType)
            ->where('creator_id', $creatorId)
            ->get();
    }

    private function storeAttachment(Event $event, UploadedFile $file, string $disk = 'public'): string
    {
        $ext = strtolower($file->getClientOriginalExtension() ?: 'bin');
        $name = Str::uuid() . '.' . $ext;
        $path = "events/{$event->id}/attachments/{$name}";
        Storage::disk($disk)->put($path, file_get_contents($file->getRealPath()));
        return $path;
    }

    private function relocateCoverToEvents(Event $event, string $sourcePath, string $disk = 'public'): string
    {
        $targetDir = "events/{$event->id}";
        $filename = basename($sourcePath);
        $target = "{$targetDir}/{$filename}";

        if (!Storage::disk($disk)->exists($targetDir)) {
            Storage::disk($disk)->makeDirectory($targetDir);
        }

        if ($sourcePath !== $target && Storage::disk($disk)->exists($sourcePath)) {
            Storage::disk($disk)->move($sourcePath, $target);
        }

        return $target;
    }

    private function deleteIfExists(?string $path, string $disk = 'public'): void
    {
        if ($path && Storage::disk($disk)->exists($path)) {
            Storage::disk($disk)->delete($path);
        }
    }

    private function maybeGeocode(Event $event, array $input): void
    {
        $mode = $event->mode ?? $input['mode'] ?? 'offline';
        if (!in_array($mode, ['offline', 'hybrid'], true)) return;

        $addressChanged = array_key_exists('address', $input)
            || array_key_exists('city', $input)
            || array_key_exists('venue_name', $input)
            || array_key_exists('country', $input);

        $missingCoords = !$event->lat || !$event->lng;

        if (!$addressChanged && !$missingCoords) return;

        $parts = [
            $event->venue_name ?? $input['venue_name'] ?? null,
            $event->address ?? $input['address'] ?? null,
            $event->city ?? $input['city'] ?? null,
            $event->country ?? $input['country'] ?? null,
        ];
        $query = implode(', ', array_filter(array_map('trim', $parts)));

        $geo = $this->geocoder->geocode($query);
        if ($geo) {
            if (!is_null($geo['lat'])) $event->lat = $geo['lat'];
            if (!is_null($geo['lng'])) $event->lng = $geo['lng'];
            if (!is_null($geo['country'])) $event->country = $geo['country'];
        }
    }
}
