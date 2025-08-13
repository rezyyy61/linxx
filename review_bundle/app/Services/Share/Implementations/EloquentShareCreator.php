<?php

namespace App\Services\Share\Implementations;

use App\Models\Share\Share;
use App\Services\Share\Contracts\LinkBuilder;
use App\Services\Share\Contracts\PermissionGate;
use App\Services\Share\Contracts\ShareCreator;
use App\Services\Share\Contracts\SlugGenerator;
use App\Services\Share\Contracts\WebhookPusher;
use App\Services\Share\DTOs\CreateShareData;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class EloquentShareCreator implements ShareCreator
{
    public function __construct(
        protected PermissionGate $gate,
        protected SlugGenerator $slugger,
        protected LinkBuilder $links,
        protected WebhookPusher $webhooks
    ) {}

    public function create(CreateShareData $data): Share
    {
        if (!$this->gate->canShare($data->actor, $data->shareable)) {
            throw ValidationException::withMessages(['shareable' => 'forbidden']);
        }

        return DB::transaction(function () use ($data) {
            $slug = $this->uniqueSlug();
            $share = new Share();
            $share->user_id = $data->actor->getAuthIdentifier();
            $share->shareable_type = $data->shareable->getMorphClass();
            $share->shareable_id = $data->shareable->getKey();
            $share->scope = $data->scope;
            $share->slug = $slug;
            $share->password_hash = $data->password ? Hash::make($data->password) : null;
            $share->expires_at = $data->expiresAt;
            $share->max_clicks = $data->maxClicks;
            $share->allow_repost = $data->allowRepost;
            $share->extra = $data->extra;
            $share->save();

            $this->webhooks->push('shares.created', $share, ['link' => $this->links->build($share)]);

            return $share;
        });
    }

    protected function uniqueSlug(): string
    {
        do {
            $slug = $this->slugger->generate();
        } while (Share::query()->where('slug', $slug)->exists());
        return $slug;
    }
}
