<?php

namespace App\Services\Share\Contracts;

use App\Models\Share\Share;
use App\Services\Share\DTOs\ClickData;

interface ClickRegistrar
{
    public function register(Share $share, ClickData $data): void;
}
