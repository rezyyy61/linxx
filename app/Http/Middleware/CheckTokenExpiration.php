<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckTokenExpiration
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->user()?->currentAccessToken();

        if ($token && $token->expires_at && $token->expires_at->isPast()) {
            $token->delete();

            return response()->json([
                'message' => 'Your token has expired. Please login again.',
            ], 401);
        }

        return $next($request);
    }
}
