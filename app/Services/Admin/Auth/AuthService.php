<?php

namespace App\Services\Admin\Auth;

use App\Models\Admin\Admin;
use App\Models\Admin\AdminLog;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function login(array $credentials): array
    {
        $admin = Admin::where('email', $credentials['email'])->first();

        if (! $admin || ! Hash::check($credentials['password'], $admin->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email or password is incorrect.'],
            ]);
        }

        if (! $admin->is_active) {
            throw ValidationException::withMessages([
                'email' => ['This admin account is deactivated.'],
            ]);
        }

        return [
            '2fa_required' => true,
            'user_id' => $admin->id,
        ];
    }

    public function generateToken(Admin $admin): array
    {
        $token = $admin->createToken('admin-token')->plainTextToken;

        $this->logLogin($admin);

        return [
            'token' => $token,
            'user' => [
                'id' => $admin->id,
                'uuid' => $admin->uuid,
                'name' => $admin->name,
                'email' => $admin->email,
                'roles' => [$admin->role],
            ],
        ];
    }

    protected function logLogin(Admin $admin): void
    {
        $request = request();

        AdminLog::create([
            'admin_id'    => $admin->id,
            'action'      => 'login',
            'description' => 'Admin logged in',
            'ip_address'  => $request->ip(),
            'user_agent'  => $request->userAgent(),
            'target_type' => 'admin',
            'target_id'   => '3',
            'created_at'  => now(),
        ]);
    }

    public function logout(Admin $admin): void
    {
        $admin->currentAccessToken()?->delete();

        AdminLog::create([
            'admin_id'    => $admin->id,
            'action'      => 'logout',
            'description' => 'Admin logged out',
            'ip_address'  => request()->ip(),
            'user_agent'  => request()->userAgent(),
            'target_type' => 'admin',
            'target_id'   => '3',
        ]);
    }
}
