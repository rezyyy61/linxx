<?php

namespace App\Services\Share\Enums;

enum ShareResolutionStatus: string
{
    case OK = 'ok';
    case NOT_FOUND = 'not_found';
    case EXPIRED = 'expired';
    case REVOKED = 'revoked';
    case FORBIDDEN = 'forbidden';
}
