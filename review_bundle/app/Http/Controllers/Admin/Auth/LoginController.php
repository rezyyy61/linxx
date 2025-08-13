<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Services\Admin\Auth\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    protected AuthService $auth;

    public function __construct(AuthService $auth)
    {
        $this->auth = $auth;
    }

    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'min:8'],
        ]);

        try {
            $data = $this->auth->login($validated);

            if (isset($data['2fa_required']) && $data['2fa_required'] === true) {
                return response()->json([
                    'status' => true,
                    'message' => '2FA required.',
                    'data' => [
                        '2fa_required' => true,
                        'user_id' => $data['user_id'],
                    ]
                ]);
            }

            return response()->json([
                'status' => true,
                'message' => 'Login successful.',
                'data' => $data
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Login failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        $admin = $request->user();

        $this->auth->logout($admin);

        return response()->json([
            'status' => true,
            'message' => 'Logged out successfully.'
        ]);
    }

}
