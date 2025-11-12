<?php

namespace App\Repositories;

use App\Models\Department;
use Illuminate\Support\Facades\DB;

class DepartmentRepository
{
    public function __construct(
        protected Department $model,
        protected UserRepository $userRepository,
        protected EmployeeRepository $employeeRepository
    ) {}

    public function getAll()
    {
        return $this->model->all();
    }

    public function getAllByEnterprise()
    {
        return $this->model->get();
    }

    public function findById($id)
    {
        return $this->model->find($id);
    }

    public function findByName($name, $enterpriseId)
    {
        return $this->model
            ->where(DB::raw('LOWER(name)'), '=', strtolower($name))
            ->where('enterprise_id', $enterpriseId)
            ->first();
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $department = $this->findById($id);
        if ($department) {
            $department->update($data);

            return $department;
        }

        return null;
    }

    private function deleteChildren($id)
    {
        $children = $this->model->where('parent_id', $id)->get();

        foreach ($children as $child) {
            $this->deleteChildren($child->id);
            $child->delete();
        }
    }

    private function clearDepartmentUser($departmentId)
    {
        $this->userRepository->clearDepartment($departmentId);
    }

    private function clearDepartmentEmployee($departmentId)
    {
        $this->employeeRepository->clearDepartment($departmentId);
    }

    public function delete($id)
    {
        $department = $this->findById($id);

        if ($department) {
            $this->deleteChildren($department->id);
            $this->clearDepartmentUser($department->id);

            return $department->delete();
        }

        return false;

    }
}
