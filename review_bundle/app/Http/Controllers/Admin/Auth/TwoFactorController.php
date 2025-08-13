<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Services\Admin\Auth\TwoFactorService;
use Illuminate\Http\Request;

class TwoFactorController extends Controller
{
    protected TwoFactorService $service;

    public function __construct(TwoFactorService $service)
    {
        $this->service = $service;
    }

    public function enable(Request $request)
    {
        $admin = $request->user('admin');

        $data = $this->service->generateSecret($admin);

        return response()->json([
            'status' => true,
            'message' => '2FA secret generated.',
            'data' => $data,
        ]);
    }

    public function verify(Request $request)
    {
        $request->validate([
            'code' => ['required', 'digits:6']
        ]);

        $admin = $request->user('admin');

        $valid = $this->service->verify($admin, $request->code);

        if (! $valid) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid verification code.',
            ], 422);
        }

        $admin->two_factor_enabled = true;
        $admin->save();

        return response()->json([
            'status' => true,
            'message' => '2FA enabled successfully.',
        ]);
    }
}
