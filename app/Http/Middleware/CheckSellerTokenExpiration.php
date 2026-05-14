<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckSellerTokenExpiration
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::guard('seller')->user();

        if ($user && method_exists($user, 'currentAccessToken')) {

            $token = $user->currentAccessToken();

            if (! $token && $request->bearerToken()) {
                return $next($request);
            }

            if ($token && $token->expires_at) {
                $expiration = Carbon::parse($token->expires_at);

                if ($expiration->isPast()) {
                    $token->delete();

                    return response()->json([
                        'message' => 'Token expirado. Faça login novamente.',
                        'code' => 'TOKEN_EXPIRED',
                    ], 401);
                }
            }
        }

        return $next($request);
    }
}
