<?php

namespace App\Http\Controllers;

use App\Http\Requests\Role\CreateRoleRequest;
use App\Http\Requests\Role\DeleteRoleRequest;
use App\Http\Requests\Role\UpdateRoleRequest;
use App\Http\Resources\Role\RoleSelectResource;
use App\Repositories\RoleRepository;
use App\Services\RoleService;
use Illuminate\Http\Request;

class RoleController extends BaseController
{
    public function __construct(private RoleRepository $repository, private RoleService $service) {}

    public function index(Request $request)
    {
        return $this->safeExecute(function () {
            $roles = $this->repository->getAllByEnterprise(['permissions']);

            return response()->json(['roles' => $roles], 200);
        }, 'Erro ao buscar roles', $request);
    }

    public function indexSelect(Request $request)
    {
        return $this->safeExecute(function () {
            $roles = $this->repository->getAllByEnterprise();

            return response()->json(['roles' => RoleSelectResource::collection($roles)], 200);
        }, 'Erro ao buscar roles', $request);
    }

    public function store(CreateRoleRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            $this->service->create($request);
            $roles = $this->repository->getAllByEnterprise(['permissions']);

            return response()->json(['roles' => $roles, 'message' => 'Role cadastrada'], 201);
        }, 'Erro ao cadastrar role', $request);
    }

    public function update(UpdateRoleRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            $this->service->update($request);
            $roles = $this->repository->getAllByEnterprise(['permissions']);

            return response()->json(['roles' => $roles, 'message' => 'Role atualizada'], 200);
        }, 'Erro ao atualizar role', $request);
    }

    public function destroy(DeleteRoleRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            $role = $this->repository->findById($request->route('roleID'));
            $role->permissions()->detach();

            $this->repository->delete($request->route('roleID'));
            $roles = $this->repository->getAllByEnterprise(['permissions']);

            return response()->json(['roles' => $roles, 'message' => 'Role excluída'], 200);
        }, 'Erro ao excluir role', $request);
    }
}
