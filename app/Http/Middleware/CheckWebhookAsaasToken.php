<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckWebhookAsaasToken
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->header('access-token');

        if (! $token || $token !== config('app.dalle_payments_access_token')) {
            return response()->json(['message' => 'Token inválido'], 403);
        }

        return $next($request);
    }
}
