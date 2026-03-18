<?php

namespace App\Http\Controllers;

use App\Http\Requests\Setting\Appearance\UpdateSettingAppearanceRequest;
use App\Repositories\SettingAppearanceRepository;
use App\Services\SettingAppearanceService;
use Illuminate\Http\Request;

class SettingAppearanceController extends BaseController
{
    public function __construct(
        private SettingAppearanceService $service,
        private SettingAppearanceRepository $repository
    ) {}

    public function show(Request $request)
    {
        return $this->safeExecute(function () {
            $appearance = $this->repository->getByEnterprise();

            return response()->json(['appearance' => $appearance], 200);
        }, 'Erro ao buscar aparência', $request);
    }

    public function update(UpdateSettingAppearanceRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            $this->service->update($request);
            $appearance = $this->repository->getByEnterprise();

            return response()->json(['appearance' => $appearance, 'message' => 'Aparência atualizada'], 200);
        }, 'Erro ao atualizar aparência', $request);
    }
}
