<?php

namespace App\Utils;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ErrorLogger
{
    public static function log(string $message, \Throwable $e, ?Request $request = null): void
    {
        $context = [
            'exception_message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ];

        if ($request) {
            $context['user_id'] = optional($request->user())->id ?? 'null';
            $context['enterprise_id'] = Auth::user()->enterprise_id ?? 'null';
        }

        Log::error($message, $context);
    }
}
