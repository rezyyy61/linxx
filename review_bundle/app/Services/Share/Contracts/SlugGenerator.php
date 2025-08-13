<?php

namespace App\Services\Share\Contracts;

interface SlugGenerator
{
    public function generate(): string;
}
