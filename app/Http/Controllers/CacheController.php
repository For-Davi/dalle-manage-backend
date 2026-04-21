<?php

namespace App\Http\Controllers;

use App\Services\CacheService;
use Illuminate\Http\Request;

class CacheController extends BaseController
{
    public function __construct(
        private CacheService $service,
    ) {}

    public function clear(Request $request)
    {
        return $this->safeTransaction(function () {
            $this->service->clearCache();

            return response()->json(['message' => 'Limpeza do cache concluída'], 200);
        }, 'Erro ao fazer limpeza de cache', $request);
    }
}
