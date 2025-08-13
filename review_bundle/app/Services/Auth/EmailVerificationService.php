<?php

namespace App\Services\Auth;

use App\Mail\EmailVerificationCodeMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class EmailVerificationService
{
    public function sendVerificationCode(User $user): void
    {
        $code = rand(100000, 999999);
        $user->verification_code = $code;
        $user->verification_expires_at = now()->addMinutes(15);
        $user->save();

        Mail::to($user->email)->send(new EmailVerificationCodeMail($code));
    }


    public function verifyCode(User $user, string $code): bool
    {
        if (
            $user->verification_code === $code &&
            $user->verification_expires_at &&
            $user->verification_expires_at->isFuture()
        ) {
            $user->is_verified = true;
            $user->verification_code = null;
            $user->verification_expires_at = null;
            $user->save();
            return true;
        }

        return false;
    }
}
