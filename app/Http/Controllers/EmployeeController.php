<?php

namespace App\Http\Controllers;

use App\DTO\Employee\FilterEmployeeDTO;
use App\Http\Requests\Employee\Action\CreateAccessLoginRequest;
use App\Http\Requests\Employee\CheckIDEmployeeRequest;
use App\Http\Requests\Employee\CreateEmployeeRequest;
use App\Http\Requests\Employee\DeleteEmployeeRequest;
use App\Http\Requests\Employee\FilterEmployeeRequest;
use App\Http\Requests\Employee\ShowEmployeeRequest;
use App\Http\Requests\Employee\UpdateEmployeeRequest;
use App\Http\Resources\Employee\EmployeeTableResource;
use App\Repositories\EmployeeRepository;
use App\Services\EmployeeService;
use App\Utils\ErrorLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeeController
{
    public function __construct(
        private EmployeeService $service,
        private EmployeeRepository $repository
    ) {}

    public function index(Request $request)
    {
        try {
            $employees = $this->repository->getAllByEnterprise($request->get('enterprise_id'));

            return response()->json(['employees' => EmployeeTableResource::collection($employees)], 200);
        } catch (\Exception $e) {
            ErrorLogger::log('Erro ao buscar funcionários:', $e, $request);

            return response()->json(['message' => 'Erro ao buscar funcionários'], 500);
        }
    }

    public function show(ShowEmployeeRequest $request)
    {
        try {
            $employee = $this->repository->findById($request->route('employeeID'));

            return response()->json(['employee' => $employee], 200);
        } catch (\Exception $e) {
            ErrorLogger::log('Erro ao buscar funcionário:', $e, $request);

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function removeAccessLogin(CheckIDEmployeeRequest $request)
    {
        try {
            DB::beginTransaction();

            $employee = $this->service->removeAccessLogin($request->employeeId);

            if ($employee) {
                DB::commit();
                $employees = $this->repository->getAllByEnterprise($request->get('enterprise_id'));

                return response()->json(['employees' => EmployeeTableResource::collection($employees), 'message' => 'Removido acesso ao sistema'], 200);
            }

        } catch (\Exception $e) {
            ErrorLogger::log('Erro ao remover acesso ao sistema:', $e, $request);

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function createAccessLogin(CreateAccessLoginRequest $request)
    {
        try {
            DB::beginTransaction();

            $employee = $this->service->createAccessLogin($request);

            if ($employee) {
                DB::commit();
                $employees = $this->repository->getAllByEnterprise($request->get('enterprise_id'));

                return response()->json(['employees' => EmployeeTableResource::collection($employees), 'message' => 'Criado acesso ao sistema'], 201);
            }

        } catch (\Exception $e) {
            ErrorLogger::log('Erro ao criar acesso ao sistema:', $e, $request);

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function filter(FilterEmployeeRequest $request)
    {
        try {
            $employeeFilterDTO = FilterEmployeeDTO::fromRequest([
                ...$request->only(['name', 'email', 'sex', 'cpf', 'cnpj', 'active', 'department', 'hasLoginAccess']),
                'enterprise_id' => $request->get('enterprise_id'),
            ]);
            $employees = $this->repository->getAllWithFilter($employeeFilterDTO);

            return response()->json(['employees' => EmployeeTableResource::collection($employees)], 200);

        } catch (\Exception $e) {
            ErrorLogger::log('Erro ao filtrar funcionários:', $e, $request);

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function store(CreateEmployeeRequest $request)
    {
        try {
            DB::beginTransaction();
            $employee = $this->service->create($request);
            if ($employee) {
                DB::commit();

                $employees = $this->repository->getAllByEnterprise($request->get('enterprise_id'));

                return response()->json(['employees' => EmployeeTableResource::collection($employees), 'message' => 'Funcionário cadastrado'], 201);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            ErrorLogger::log('Erro ao cadastrar funcionário:', $e, $request);

            return response()->json(['message' => 'Erro ao cadastrar funcionário'], 500);
        }
    }

    public function update(UpdateEmployeeRequest $request)
    {
        try {
            DB::beginTransaction();
            $employee = $this->service->update($request);

            if ($employee) {
                DB::commit();

                $employees = $this->repository->getAllByEnterprise($request->get('enterprise_id'));

                return response()->json(['employees' => EmployeeTableResource::collection($employees), 'message' => 'Funcionário atualizado'], 200);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            ErrorLogger::log('Erro ao atualizar funcionário:', $e, $request);

            return response()->json(['message' => 'Erro ao atualizar funcionário'], 500);
        }
    }

    public function destroy(DeleteEmployeeRequest $request)
    {
        try {
            DB::beginTransaction();

            $employee = $this->repository->delete($request->route('employeeID'));

            if ($employee) {
                DB::commit();
                $employees = $this->repository->getAllByEnterprise($request->get('enterprise_id'));

                return response()->json(['employees' => EmployeeTableResource::collection($employees), 'message' => 'Funcionário excluído'], 200);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            ErrorLogger::log('Erro ao excluir funcionário:', $e, $request);

            return response()->json(['message' => 'Erro ao excluir funcionário'], 500);
        }
    }
}
