<?php

namespace App\Services\Share\Contracts;

use App\Models\Share\Share;

interface LinkBuilder
{
    public function build(Share $share): string;
}
