<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }


    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|string|email|unique:users',
            'password'              => 'required|string|min:8|confirmed',
        ]);

        $result = $this->authService->register($validated);
        return response()->json($result, 201);
    }


    public function login(Request $request)
    {
        $validated = $request->validate([
            'email'    => 'required|string|email',
            'password' => 'required|string',
        ]);

        $result = $this->authService->login($validated);

        if (!empty($result['error']) && $result['error'] === true) {
            return response()->json(['message' => $result['message']], $result['status']);
        }

        return response()->json($result);
    }


    public function verifyEmail(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
            'code'  => 'required|string|size:6',
        ]);

        $result = $this->authService->verifyEmail($validated['email'], $validated['code']);

        if (!empty($result['error']) && $result['error'] === true) {
            return response()->json(['message' => $result['message']], $result['status']);
        }

        return response()->json($result);
    }


    public function resendCode(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $result = $this->authService->resendCode($validated['email']);

        if (!empty($result['error']) && $result['error'] === true) {
            return response()->json(['message' => $result['message']], $result['status']);
        }

        return response()->json($result);
    }


    public function me(Request $request)
    {
        $user = $request->user();

        $profile = $user->politicalProfile()
            ->select(['user_id', 'avatar_color', 'logo_path'])
            ->first();

        return response()->json([
            'id' => $user->id,
            'uuid' => $user->uuid,
            'slug' => $user->slug,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'avatar_color' => $profile?->avatar_color,
            'logo_url' => $profile?->logo_url,
        ]);
    }


    public function logout(Request $request)
    {
        $result = $this->authService->logout($request->user());
        return response()->json($result);
    }


    public function logoutAll(Request $request)
    {
        $result = $this->authService->logoutAll($request->user());
        return response()->json($result);
    }
}
