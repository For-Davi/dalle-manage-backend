<?php

namespace App\Http\Controllers;

use App\Utils\ErrorLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class BaseController
{
    protected function safeExecute(
        callable $callback,
        string $errorMessage,
        $request = null
    ): JsonResponse {
        try {
            return $callback();

        } catch (\Exception $e) {
            ErrorLogger::critical($errorMessage, $e, $request);

            return response()->json([
                'message' => $errorMessage.' : '.$e->getMessage(),
            ], 500);
        }
    }

    protected function safeTransaction(
        callable $callback,
        string $errorMessage,
        $request = null
    ): JsonResponse {
        try {
            DB::beginTransaction();

            $response = $callback();

            DB::commit();

            return $response;

        } catch (\Exception $e) {
            DB::rollBack();

            ErrorLogger::critical($errorMessage, $e, $request);

            return response()->json([
                'message' => $errorMessage.' : '.$e->getMessage(),
            ], 500);
        }
    }
}
