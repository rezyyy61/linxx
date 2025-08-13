<?php

namespace App\Services\Share\Implementations;

use App\Models\Share\Share;
use App\Services\Share\Contracts\LinkBuilder;
use Illuminate\Support\Facades\URL;

class DefaultLinkBuilder implements LinkBuilder
{
    public function build(Share $share): string
    {
        return URL::to('/r/'.$share->slug);
    }
}
