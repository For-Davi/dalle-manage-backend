<?php

namespace App\Http\Controllers;

use App\Http\Requests\Setting\Appearance\UpdateSettingAppearanceRequest;
use App\Repositories\SettingAppearanceRepository;
use App\Services\SettingAppearanceService;
use App\Utils\ErrorLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingAppearanceController
{
    public function __construct(
        private SettingAppearanceService $service,
        private SettingAppearanceRepository $repository
    ) {}

    public function show(Request $request)
    {
        try {
            $appearance = $this->repository->getByEnterprise();

            return response()->json(['appearance' => $appearance], 200);
        } catch (\Exception $e) {
            ErrorLogger::log('Erro ao buscar aparência:', $e, $request);

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function update(UpdateSettingAppearanceRequest $request)
    {
        try {
            DB::beginTransaction();
            $appearance = $this->service->update($request);

            if ($appearance) {
                DB::commit();

                $appearance = $this->repository->getByEnterprise();

                return response()->json(['appearance' => $appearance, 'message' => 'Aparência atualizada'], 200);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            ErrorLogger::log('Erro ao atualizar aparência:', $e, $request);

            return response()->json(['message' => 'Erro ao atualizar aparência'], 500);
        }
    }
}
