<?php

namespace App\Services\Admin\Auth;

use App\Models\Admin\Admin;
use Illuminate\Support\Facades\Log;
use OTPHP\TOTP;

class TwoFactorService
{
    public function generateSecret(Admin $admin): array
    {
        Log::info('Generating secret for admin: ' . $admin->email);

        $totp = TOTP::create();
        $totp->setLabel($admin->email);
        $totp->setIssuer('Linxx App');

        $admin->two_factor_secret = $totp->getSecret();
        $admin->save();

        return [
            'qr_code_url' => $totp->getProvisioningUri(),
            'secret' => $totp->getSecret(),
        ];
    }

    public function verify(Admin $admin, string $code): bool
    {
        if (! $admin->two_factor_secret) {
            return false;
        }

        $totp = TOTP::create($admin->two_factor_secret);
        return $totp->verify($code);
    }
}
