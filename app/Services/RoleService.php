<?php

namespace App\Services;

use App\DTO\Role\CreateRoleDTO;
use App\DTO\Role\UpdateRoleDTO;
use App\Helpers\RoleHelper;
use App\Repositories\RoleRepository;

class RoleService
{
    public function __construct(protected RoleRepository $repository) {}

    public function create($request)
    {
        RoleHelper::existsRole($request->name, 'create');

        $roleDTO = CreateRoleDTO::fromRequest($request);
        $role = $this->repository->create($roleDTO->toArray());
        $this->syncPermissionsRole($role, $request->permissions);

        return $role;
    }

    public function update($request)
    {
        RoleHelper::existsRole($request->name, 'update', $request->id);

        $roleDTO = UpdateRoleDTO::fromRequest($request);
        $role = $this->repository->update($request->id, $roleDTO->toArray());
        $this->syncPermissionsRole($role, $request->permissions);

        return $role;
    }

    private function syncPermissionsRole($role, array $permissionIds)
    {
        $role->permissions()->sync($permissionIds);
    }
}
