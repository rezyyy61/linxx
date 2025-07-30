<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\PoliticalProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'user',
        ]);

        $avatarColors = [
            '#EF4444', '#F59E0B', '#10B981',
            '#3B82F6', '#8B5CF6', '#EC4899',
            '#14B8A6', '#F43F5E'
        ];
        $randomColor = $avatarColors[array_rand($avatarColors)];

        PoliticalProfile::create([
            'user_id'      => $user->id,
            'group_name'   => $user->name,
            'tagline'      => '',
            'avatar_color' => $randomColor,
        ]);
        $token = $user->createToken('auth_token')->plainTextToken;
        $user->tokens()->latest()->first()->update(['expires_at' => now()->addHours(24)]);

        $user->load('politicalProfile');

        return response()->json([
            'message' => 'Registration successful',
            'user'    => new UserResource($user),
            'token'   => $token,
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|string|email',
            'password' => 'required|string',
        ]);

        $key = 'login:' . Str::lower($request->email) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'message' => "Too many login attempts. Please try again in $seconds seconds.",
            ], 429);
        }

        RateLimiter::hit($key, 60);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid email or password.'],
            ]);
        }

        RateLimiter::clear($key);

        $token = $user->createToken('auth_token')->plainTextToken;
        $user->tokens()->latest()->first()->update(['expires_at' => now()->addHours(24)]);

        $user->load('politicalProfile');

        return response()->json([
            'message' => 'Login successful',
            'user'    => new UserResource($user),
            'token'   => $token,
        ]);
    }

    public function me(Request $request)
    {
        $user = $request->user()->load('politicalProfile');

        return new UserResource($user);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout successful']);
    }

    public function logoutAll(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'All sessions have been logged out']);
    }
}
