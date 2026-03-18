<?php

namespace App\Http\Controllers;

use App\Http\Requests\Department\CreateDepartmentRequest;
use App\Http\Requests\Department\DeleteDepartmentRequest;
use App\Http\Requests\Department\UpdateDepartmentRequest;
use App\Repositories\DepartmentRepository;
use App\Services\DepartmentService;
use Illuminate\Http\Request;

class DepartmentController extends BaseController
{
    public function __construct(
        private DepartmentService $service,
        private DepartmentRepository $repository
    ) {}

    public function index(Request $request)
    {
        return $this->safeExecute(function () {
            $departments = $this->repository->getAllByEnterprise();

            return response()->json(['departments' => $departments], 200);
        }, 'Erro ao buscar departamentos', $request);
    }

    public function store(CreateDepartmentRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            $this->service->create($request);
            $departments = $this->repository->getAllByEnterprise();

            return response()->json(['departments' => $departments, 'message' => 'Departamento cadastrado'], 201);
        }, 'Erro ao cadastrar departamento', $request);
    }

    public function update(UpdateDepartmentRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            $this->service->update($request);
            $departments = $this->repository->getAllByEnterprise();

            return response()->json(['departments' => $departments, 'message' => 'Departamento atualizado'], 200);
        }, 'Erro ao atualizar departamento', $request);
    }

    public function destroy(DeleteDepartmentRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            $this->repository->delete($request->route('departmentID'));
            $departments = $this->repository->getAllByEnterprise();

            return response()->json(['departments' => $departments, 'message' => 'Departamento excluído'], 200);
        }, 'Erro ao excluir departamento', $request);
    }
}
