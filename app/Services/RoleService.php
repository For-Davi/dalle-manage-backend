<?php

namespace App\Services;

use App\DTO\Role\CreateRoleDTO;
use App\DTO\Role\UpdateRoleDTO;
use App\Helpers\RoleHelper;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;

class RoleService
{
    public function __construct(protected RoleRepository $repository, protected UserRepository $userRepository) {}

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

    public function delete($request)
    {
        $role = $this->repository->findById($request->route('roleID'));

        $role->permissions()->detach();
        $this->updateAllRolesInUsers($request->route('roleID'), $request->route('newRoleID'));
        $this->repository->delete($request->route('roleID'));
    }

    private function syncPermissionsRole($role, array $permissionIds)
    {
        $role->permissions()->sync($permissionIds);
    }

    private function updateAllRolesInUsers($roleID, $newRoleID)
    {
        $this->userRepository->updateByRole($roleID, $newRoleID);
    }
}
