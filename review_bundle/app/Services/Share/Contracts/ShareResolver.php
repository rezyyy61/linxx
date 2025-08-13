<?php

namespace App\Services\Share\Contracts;

use App\Services\Share\Results\ShareResolution;

interface ShareResolver
{
    public function resolveBySlug(string $slug, ?string $password = null): ShareResolution;
}
