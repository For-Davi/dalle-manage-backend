<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class DepartmentHelper
{
    public static function existsDepartment($id, $name, $mode)
    {
        $department = DB::table('departments')
            ->where('name', $name)
            ->where('enterprise_id', Auth::user()->enterprise_id)
            ->first();

        if ($mode === 'create') {
            if ($department) {
                throw ValidationException::withMessages([
                    'name' => ['Já existe um departamento igual ou parecido.'],
                ]);
            }
        } else {
            if ($department && $department->id != $id) {
                throw ValidationException::withMessages([
                    'name' => ['Já existe um departamento igual ou parecido.'],
                ]);
            }
        }
    }
}
