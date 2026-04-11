<?php

namespace App\Http\Controllers;

use App\Http\Requests\Setting\System\UpdateSettingSystemRequest;
use App\Repositories\SettingSystemRepository;
use App\Services\SettingSystemService;
use Illuminate\Http\Request;

class SettingSystemController extends BaseController
{
    public function __construct(
        private SettingSystemService $service,
        private SettingSystemRepository $repository
    ) {}

    public function show(Request $request)
    {
        return $this->safeExecute(function () {
            $system = $this->repository->getByEnterprise();

            return response()->json(['system' => $system], 200);
        }, 'Erro ao buscar sistema', $request);
    }

    public function update(UpdateSettingSystemRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            check_permission('config.update');
            $this->service->update($request);
            $system = $this->repository->getByEnterprise();

            return response()->json(['system' => $system, 'message' => 'Sistema atualizado'], 200);
        }, 'Erro ao atualizar sistema', $request);
    }
}
