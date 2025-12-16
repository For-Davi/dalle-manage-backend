<?php

namespace App\Http\Controllers;

use App\Http\Requests\Dashboard\GetInfoDashboardRequest;
use Illuminate\Http\Request;
use App\Services\DashboardService;
use App\Utils\ErrorLogger;

class DashboardController
{
    public function __construct(
        private DashboardService $service,
    ) {}

    public function index(Request $request)
    {
        try {
            $info = $this->service->getInfo();

            return response()->json(['info' => $info], 200);
        } catch (\Exception $e) {
            ErrorLogger::log('Erro ao buscar dados do dashboard:', $e, $request);

            return response()->json(['message' => 'Erro ao buscar dados do dashboard'], 500);
        }
    }
}
