<?php

namespace App\Services\Auth;

use App\Http\Resources\UserResource;
use App\Models\PoliticalProfile;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthService
{
    protected EmailVerificationService $emailVerificationService;

    public function __construct(EmailVerificationService $emailVerificationService)
    {
        $this->emailVerificationService = $emailVerificationService;
    }

    public function register(array $data): array
    {
        $user = User::create([
            'name'        => $data['name'],
            'email'       => $data['email'],
            'password'    => Hash::make($data['password']),
            'role'        => 'user',
            'is_verified' => false
        ]);

        $avatarColors = ['#EF4444', '#F59E0B', '#10B981', '#3B82F6', '#8B5CF6', '#EC4899', '#14B8A6', '#F43F5E'];
        $randomColor  = $avatarColors[array_rand($avatarColors)];

        PoliticalProfile::create([
            'user_id'      => $user->id,
            'group_name'   => $user->name,
            'entity_type'  => 'individual',
            'tagline'      => '',
            'avatar_color' => $randomColor,
        ]);

        $this->emailVerificationService->sendVerificationCode($user);

        return [
            'message' => 'Registration successful. Verification code sent to email.',
            'user'    => new UserResource($user->load('politicalProfile')),
        ];
    }

    public function login(array $data): array
    {
        $key = 'login:' . Str::lower($data['email']) . '|' . request()->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return [
                'error'   => true,
                'status'  => 429,
                'message' => "Too many login attempts. Please try again in $seconds seconds."
            ];
        }

        RateLimiter::hit($key, 60);

        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid email or password.'],
            ]);
        }

        if (!$user->is_verified) {
            $this->emailVerificationService->sendVerificationCode($user);
            return [
                'error'   => true,
                'status'  => 403,
                'message' => 'Email not verified. Verification code sent.'
            ];
        }

        RateLimiter::clear($key);

        $token = $this->createUserToken($user);

        return [
            'message' => 'Login successful',
            'user'    => new UserResource($user->load('politicalProfile')),
            'token'   => $token,
        ];
    }

    public function verifyEmail(string $email, string $code): array
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            return [
                'error'   => true,
                'status'  => 404,
                'message' => 'User not found.'
            ];
        }

        if ($this->emailVerificationService->verifyCode($user, $code)) {
            $token = $this->createUserToken($user);

            return [
                'message' => 'Email verified successfully.',
                'user'    => new UserResource($user->load('politicalProfile')),
                'token'   => $token
            ];
        }

        return [
            'error'   => true,
            'status'  => 422,
            'message' => 'Invalid or expired code.'
        ];
    }

    public function resendCode(string $email): array
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            return [
                'error'   => true,
                'status'  => 404,
                'message' => 'User not found.'
            ];
        }

        $this->emailVerificationService->sendVerificationCode($user);

        return [
            'message' => 'Verification code resent successfully.'
        ];
    }

    public function logout(User $user): array
    {
        $user->currentAccessToken()->delete();
        return ['message' => 'Logout successful'];
    }

    public function logoutAll(User $user): array
    {
        $user->tokens()->delete();
        return ['message' => 'All sessions have been logged out'];
    }

    private function createUserToken(User $user): string
    {
        $token = $user->createToken('auth_token')->plainTextToken;
        $user->tokens()->latest()->first()->update([
            'expires_at' => now()->addHours(24)
        ]);
        return $token;
    }
}
