<?php

namespace App\Http\Controllers;

use App\Http\Requests\Setting\System\UpdateSettingSystemRequest;
use App\Repositories\SettingSystemRepository;
use App\Services\SettingSystemService;
use App\Utils\ErrorLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingSystemController
{
    public function __construct(
        private SettingSystemService $service,
        private SettingSystemRepository $repository
    ) {}

    public function show(Request $request)
    {
        try {
            $system = $this->repository->getByEnterprise($request->get('enterprise_id'));

            return response()->json(['system' => $system], 200);
        } catch (\Exception $e) {
            ErrorLogger::log('Erro ao buscar sistema:', $e, $request);

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function update(UpdateSettingSystemRequest $request)
    {
        try {
            DB::beginTransaction();
            $system = $this->service->update($request);

            if ($system) {
                DB::commit();

                $system = $this->repository->getByEnterprise($request->get('enterprise_id'));

                return response()->json(['system' => $system, 'message' => 'Sistema atualizado'], 200);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            ErrorLogger::log('Erro ao atualizar sistema:', $e, $request);

            return response()->json(['message' => 'Erro ao atualizar sistema'], 500);
        }
    }
}
