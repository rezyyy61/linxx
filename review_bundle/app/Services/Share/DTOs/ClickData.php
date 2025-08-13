<?php

namespace App\Services\Share\DTOs;

class ClickData
{
    public function __construct(
        public readonly ?string $ip = null,
        public readonly ?string $userAgent = null,
        public readonly ?string $referrerFull = null,
        public readonly ?string $utmSource = null,
        public readonly ?string $utmMedium = null,
        public readonly ?string $utmCampaign = null,
        public readonly ?string $countryCode = null,
        public readonly ?string $deviceType = null,
        public readonly ?string $fingerprint = null
    ) {}
}
