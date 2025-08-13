<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\PoliticalProfile;
use App\Models\User;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    public function redirect($provider)
    {
        return Socialite::driver($provider)->stateless()->redirect();
    }


    public function callback($provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->stateless()->user();
        } catch (\Exception $e) {
            return redirect(config('app.frontend_url') . '/login?error=social_login_failed');
        }

        $user = User::firstOrCreate(
            ['email' => $socialUser->getEmail()],
            [
                'name'        => $socialUser->getName() ?? $socialUser->getNickname(),
                'password'    => bcrypt(Str::random(16)), // رمز عبور تصادفی
                'is_verified' => true
            ]
        );

        if (!$user->politicalProfile) {
            $avatarColors = ['#EF4444', '#F59E0B', '#10B981', '#3B82F6', '#8B5CF6', '#EC4899', '#14B8A6', '#F43F5E'];
            $defaultColor = config('app.default_avatar_color', $avatarColors[array_rand($avatarColors)]);

            PoliticalProfile::create([
                'user_id'      => $user->id,
                'group_name'   => $user->name,
                'entity_type'  => 'individual',
                'tagline'      => '',
                'avatar_color' => $defaultColor,
                'logo_path'    => $socialUser->getAvatar(),
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        $frontendUrl = config('app.frontend_url') . '/social-login/callback';
        return redirect()->away($frontendUrl . '?token=' . $token . '&user=' . urlencode(json_encode(new UserResource($user->load('politicalProfile')))));
    }
}