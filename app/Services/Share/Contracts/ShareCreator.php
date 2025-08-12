<?php

namespace App\Services\Share\Contracts;

use App\Models\Share\Share;
use App\Services\Share\DTOs\CreateShareData;

interface ShareCreator
{
    public function create(CreateShareData $data): Share;
}
