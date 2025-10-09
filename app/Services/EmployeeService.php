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
        $userId = null;

        if ($request->hasLoginAccess === 1) {
            $userDTO = UserStartDTO::fromRequest([
                ...$request->only(['name', 'password', 'email', 'roleId', 'departmentId']),
                'enterpriseID' => $request->get('enterprise_id'),
            ]);

            $user = $this->createUser($userDTO->toArray());
            $userId = $user->id;
        }

        $employeeDTO = CreateEmployeeDTO::fromRequest([
            ...$request->only([
                'name',
                'email',
                'sex',
                'phone',
                'cpf',
                'cnpj',
                'stateRegistration',
                'municipalRegistration',
                'dateBirthday',
                'cep',
                'country',
                'state',
                'city',
                'neighborhood',
                'address',
                'number',
                'complement',
                'description',
                'hasLoginAccess',
                'departmentId',
            ]),
            'userId' => $userId,
            'enterpriseId' => $request->get('enterprise_id'),
        ]);

        return $this->repository->create($employeeDTO->toArray());
    }

    public function update($request)
    {
        $employeeDTO = UpdateEmployeeDTO::fromRequest([
            ...$request->only([
                'name',
                'email',
                'sex',
                'phone',
                'cpf',
                'cnpj',
                'stateRegistration',
                'municipalRegistration',
                'dateBirthday',
                'cep',
                'country',
                'state',
                'city',
                'neighborhood',
                'address',
                'number',
                'complement',
                'description',
                'departmentId',
                'active',
            ]),
        ]);

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
            'enterprise_id' => $request->get('enterprise_id'),
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
