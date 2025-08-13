<?php

namespace App\Services\Share\Contracts;


use App\Models\Share\Share;

interface ShareRevoker
{
    public function revoke(Share $share): void;
}
