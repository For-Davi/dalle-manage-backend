<?php

namespace App\Repositories;

use App\Models\Department;
use App\Repositories\Base\BaseRepository;
use Illuminate\Support\Facades\DB;

class DepartmentRepository extends BaseRepository
{
    public function __construct(
        Department $model,
        protected UserRepository $userRepository,
        protected EmployeeRepository $employeeRepository
    ) {
        parent::__construct($model);
    }

    public function findByName($name, $enterpriseId)
    {
        return $this->model
            ->where(DB::raw('LOWER(name)'), '=', strtolower($name))
            ->where('enterprise_id', $enterpriseId)
            ->first();
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
            $this->clearDepartmentEmployee($department->id);

            return $department->delete();
        }

        return false;

    }
}
