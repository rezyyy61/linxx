<?php

namespace App\Services\Share\Results;

use App\Models\Share\Share;
use App\Services\Share\Enums\ShareResolutionStatus;

class ShareResolution
{
    public function __construct(
        public readonly ShareResolutionStatus $status,
        public readonly ?Share $share = null
    ) {}
}
