<?php

namespace App\Http\Controllers;

use App\Utils\ErrorLogger;
use Illuminate\Support\Facades\DB;
// Importe a classe base de Response
use Symfony\Component\HttpFoundation\Response;

class BaseController
{
    protected function safeExecute(
        callable $callback,
        string $errorMessage,
        $request = null
    ): Response {
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
    ): Response {
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
