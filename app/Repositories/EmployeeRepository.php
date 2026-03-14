<?php

namespace App\Repositories;

use App\DTO\Employee\FilterEmployeeDTO;
use App\Models\Employee;
use App\Repositories\Base\BaseRepository;
use Illuminate\Support\Facades\DB;

class EmployeeRepository extends BaseRepository
{
    public function __construct(Employee $model)
    {
        parent::__construct($model);
    }

    public function getAllWithFilter(FilterEmployeeDTO $filters)
    {
        $query = $this->model->where('enterprise_id', $filters->enterprise_id);

        if ($filters->name !== null) {
            $query->where('name', 'like', "%{$filters->name}%");
        }

        if ($filters->email !== null) {
            $query->where('email', 'like', "%{$filters->email}%");
        }
        if ($filters->cpf !== null) {
            $query->where('cpf', 'like', "%{$filters->cpf}%");
        }

        if ($filters->cnpj !== null) {
            $query->where('cnpj', 'like', "%{$filters->cnpj}%");
        }

        if ($filters->sex !== null) {
            $query->where('sex', $filters->sex);
        }

        if ($filters->active !== null) {
            $query->where('active', $filters->active);
        }

        if ($filters->has_login_access !== null) {
            $query->where('has_login_access', $filters->has_login_access);
        }

        if ($filters->department_id !== null) {
            $query->where('department_id', $filters->department_id);
        }

        return $query->get();
    }

    public function clearDepartment($departmentId)
    {
        $this->model->where('department_id', $departmentId)->update(['department_id' => null]);
    }

    public function delete($id)
    {
        $employee = $this->findById($id);

        if ($employee) {

            DB::table('users')->where('id', $employee->user_id)->delete();
            DB::table('sales')->where('seller_id', $id)->update(['seller_id' => null]);
            DB::table('returns')->where('seller_id', $id)->update(['seller_id' => null]);
            DB::table('commissions')->where('seller_id', $id)->update(['seller_id' => null]);

            $employee->delete();

            return true;
        }

        return false;
    }
}
