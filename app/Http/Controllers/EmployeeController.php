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
use Illuminate\Http\Request;

class EmployeeController extends BaseController
{
    public function __construct(
        private EmployeeService $service,
        private EmployeeRepository $repository
    ) {}

    public function index(Request $request)
    {
        return $this->safeExecute(function () {
            $employees = $this->repository->getAllByEnterprise();

            return response()->json(['employees' => EmployeeTableResource::collection($employees)], 200);
        }, 'Erro ao buscar funcionários', $request);
    }

    public function show(ShowEmployeeRequest $request)
    {
        return $this->safeExecute(function () use ($request) {
            $employee = $this->repository->findById($request->route('employeeID'));

            return response()->json(['employee' => $employee], 200);
        }, 'Erro ao buscar funcionário', $request);
    }

    public function removeAccessLogin(CheckIDEmployeeRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            check_permission('employee.update');
            $this->service->removeAccessLogin($request->employeeId);
            $employees = $this->repository->getAllByEnterprise();

            return response()->json(['employees' => EmployeeTableResource::collection($employees), 'message' => 'Removido acesso ao sistema'], 200);
        }, 'Erro ao remover acesso ao sistema', $request);
    }

    public function createAccessLogin(CreateAccessLoginRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            check_permission('employee.update');
            $this->service->createAccessLogin($request);
            $employees = $this->repository->getAllByEnterprise();

            return response()->json(['employees' => EmployeeTableResource::collection($employees), 'message' => 'Criado acesso ao sistema'], 201);
        }, 'Erro ao criar acesso ao sistema', $request);
    }

    public function filter(FilterEmployeeRequest $request)
    {
        return $this->safeExecute(function () use ($request) {
            $employeeFilterDTO = FilterEmployeeDTO::fromRequest($request);
            $employees = $this->repository->getAllWithFilter($employeeFilterDTO);

            return response()->json(['employees' => EmployeeTableResource::collection($employees)], 200);
        }, 'Erro ao filtrar funcionários', $request);
    }

    public function store(CreateEmployeeRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            check_plan('employees');
            check_permission('employee.create');
            $this->service->create($request);
            $employees = $this->repository->getAllByEnterprise();

            return response()->json(['employees' => EmployeeTableResource::collection($employees), 'message' => 'Funcionário cadastrado'], 201);
        }, 'Erro ao cadastrar funcionário', $request);
    }

    public function update(UpdateEmployeeRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            check_permission('employee.update');
            $this->service->update($request);
            $employees = $this->repository->getAllByEnterprise();

            return response()->json(['employees' => EmployeeTableResource::collection($employees), 'message' => 'Funcionário atualizado'], 200);
        }, 'Erro ao atualizar funcionário', $request);
    }

    public function destroy(DeleteEmployeeRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            check_permission('employee.delete');
            $this->repository->delete($request->route('employeeID'));
            $employees = $this->repository->getAllByEnterprise();

            return response()->json(['employees' => EmployeeTableResource::collection($employees), 'message' => 'Funcionário excluído'], 200);
        }, 'Erro ao excluir funcionário', $request);
    }
}
