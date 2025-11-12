<?php

namespace App\Services;

use App\DTO\Department\CreateDepartmentDTO;
use App\DTO\Department\UpdateDepartmentDTO;
use App\Helpers\DepartmentHelper;
use App\Repositories\DepartmentRepository;

class DepartmentService
{
    public function __construct(protected DepartmentRepository $repository) {}

    public function create($request)
    {
        DepartmentHelper::existsDepartment(
            null,
            $request->input('name'),
            'create'
        );

        $departmentDTO = CreateDepartmentDTO::fromRequest($request);

        return $this->repository->create($departmentDTO->toArray());
    }

    public function update($request)
    {
        DepartmentHelper::existsDepartment(
            $request->input('id'),
            $request->input('name'),
            'update'
        );

        $departmentDTO = UpdateDepartmentDTO::fromRequest($request);

        return $this->repository->update($request->input('id'), $departmentDTO->toArray());
    }
}
