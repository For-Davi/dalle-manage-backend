<?php

namespace App\Http\Controllers;

use App\Http\Requests\Dashboard\FilterDashboardRequest;
use App\Services\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends BaseController
{
    public function __construct(
        private DashboardService $service,
    ) {}

    public function index(Request $request)
    {
        return $this->safeExecute(function () {
            $info = $this->service->getInfo();

            return response()->json(['info' => $info], 200);
        }, 'Erro ao buscar dados do dashboard', $request);
    }

    public function filter(FilterDashboardRequest $request)
    {
        return $this->safeExecute(function () use ($request) {
            $info = $this->service->getInfoFilter($request);

            return response()->json(['info' => $info], 200);
        }, 'Erro ao filtrar dashboard', $request);
    }
}
