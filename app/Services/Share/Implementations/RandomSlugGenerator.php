<?php

namespace App\Services\Share\Implementations;

use App\Services\Share\Contracts\SlugGenerator;
use Illuminate\Support\Str;

class RandomSlugGenerator implements SlugGenerator
{
    public function generate(): string
    {
        return Str::lower(Str::random(10));
    }
}
