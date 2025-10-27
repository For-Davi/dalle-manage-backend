<?php

namespace App\Services;

use App\DTO\Employee\CreateEmployeeDTO;
use App\DTO\Employee\UpdateEmployeeDTO;
use App\DTO\User\CreateUserDTO;
use App\DTO\User\UserStartDTO;
use App\Repositories\EmployeeRepository;
use App\Repositories\UserRepository;

class EmployeeService
{
    public function __construct(
        protected EmployeeRepository $repository,
        protected UserRepository $userRepository
    ) {}

    private function createUser($userDTO)
    {
        return $this->userRepository->create($userDTO);
    }

    public function create($request)
    {
        $userID = null;

        if ($request->hasLoginAccess === 1) {
            $userDTO = UserStartDTO::fromRequest($request);

            $user = $this->createUser($userDTO->toArray());
            $userID = $user->id;
        }

        $employeeDTO = CreateEmployeeDTO::fromRequest($request, $userID);

        return $this->repository->create($employeeDTO->toArray());
    }

    public function update($request)
    {
        $employeeDTO = UpdateEmployeeDTO::fromRequest($request);

        return $this->repository->update($request->id, $employeeDTO->toArray());
    }

    public function createAccessLogin($request)
    {
        $employee = $this->repository->findById($request->employeeId);

        $userDTO = CreateUserDTO::fromRequest([
            'name' => $employee->name,
            'email' => $employee->email,
            'roleId' => $request->roleId,
            'departmentId' => $employee->department_id,
            'password' => $request->password,
        ]);

        $user = $this->createUser($userDTO->toArray());

        return $this->repository->update($request->employeeId, ['user_id' => $user->id, 'has_login_access' => 1]);
    }

    public function removeAccessLogin($employeeID)
    {
        $employee = $this->repository->findById($employeeID);

        return $this->userRepository->delete($employee->user_id);
    }
}
