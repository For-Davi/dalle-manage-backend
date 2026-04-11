<?php

namespace App\Http\Controllers;

use App\Http\Requests\Enterprise\UpdateEnterpriseRequest;
use App\Repositories\EnterpriseRepository;
use App\Services\EnterpriseService;
use Illuminate\Http\Request;

class EnterpriseController extends BaseController
{
    public function __construct(
        private EnterpriseRepository $repository,
        private EnterpriseService $service
    ) {}

    public function show(Request $request)
    {
        return $this->safeExecute(function () {
            $enterprise = $this->repository->findMyEnterprise();

            return response()->json(['enterprise' => $enterprise], 200);
        }, 'Erro ao buscar empresa', $request);
    }

    public function update(UpdateEnterpriseRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            check_permission('enterprise.update');
            $this->service->update($request);

            return response()->json(['message' => 'Dados da empresa atualizados'], 200);
        }, 'Erro ao atualizar dados da empresa', $request);
    }

    public function destroy(Request $request)
    {
        return $this->safeTransaction(function () {
            check_permission('enterprise.delete');
            $this->repository->delete();

            return response()->json(['message' => 'Empresa deletada'], 200);
        }, 'Erro ao deletar a empresa', $request);
    }
}
