<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin\Admin;
use App\Services\Admin\Auth\AuthService;
use App\Services\Admin\Auth\TwoFactorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use OTPHP\TOTP;

class TwoFactorLoginController extends Controller
{
    protected TwoFactorService $service;
    protected AuthService $auth;

    public function __construct(TwoFactorService $service, AuthService $auth)
    {
        $this->service = $service;
        $this->auth = $auth;
    }

    public function verify(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => ['required', 'exists:admins,id'],
            'code' => ['required', 'digits:6'],
        ]);

        $admin = Admin::findOrFail($request->user_id);

        if (! $admin->two_factor_enabled || ! $admin->two_factor_secret) {
            return response()->json([
                'status' => false,
                'message' => 'Two-factor authentication is not enabled for this user.'
            ], 403);
        }

        if (! $this->service->verify($admin, $request->code)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid 2FA code.'
            ], 422);
        }

        $data = $this->auth->generateToken($admin);

        return response()->json([
            'status' => true,
            'message' => 'Login successful.',
            'token' => $data['token'],
            'user' => $data['user'],
        ]);
    }

    public function setup(Request $request)
    {
        $admin = Admin::findOrFail($request->user_id);

        if ($admin->two_factor_enabled && $admin->two_factor_secret) {
            Log::info('2FA already active');
            return response()->json([
                'status' => true,
                'message' => '2FA already enabled.',
                'data' => null,
            ]);
        }

        if (!$admin->two_factor_secret) {
            Log::info('generate');
            $data = $this->service->generateSecret($admin);
        } else {
            $totp = TOTP::create($admin->two_factor_secret);
            $totp->setLabel($admin->email);
            $totp->setIssuer('Linxx App');

            $data = [
                'qr_code_url' => $totp->getProvisioningUri(),
                'secret' => $admin->two_factor_secret,
            ];
        }

        return response()->json([
            'status' => true,
            'message' => '2FA setup ready.',
            'data' => $data,
        ]);
    }
}
