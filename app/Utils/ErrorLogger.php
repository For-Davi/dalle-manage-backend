<?php

namespace App\Utils;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ErrorLogger
{
    public static function critical(string $message, \Throwable $exception, $request = null)
    {
        Log::critical($message, self::context($exception, $request));
    }

    public static function error(string $message, \Throwable $exception, $request = null)
    {
        Log::error($message, self::context($exception, $request));
    }

    private static function context(\Throwable $exception, $request = null): array
    {
        return [
            'exception' => class_basename($exception),
            'message' => $exception->getMessage(),
            'file' => $exception->getFile().':'.$exception->getLine(),
            'request' => $request ? [
                'ip' => $request->ip(),
                'route' => $request->route()?->getName(),
                'method' => $request->method(),
                'user_id' => Auth::id(),
            ] : null,
        ];
    }
}
