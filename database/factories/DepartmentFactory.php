<?php

namespace Database\Factories;

use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

class DepartmentFactory extends Factory
{
    protected $model = Department::class;

    public function definition()
    {
        $departments = [
            'FINANCEIRO',
            'RECURSOS HUMANOS',
            'COMERCIAL',
            'LOGÍSTICA',
            'MARKETING',
            'TI',
            'ATENDIMENTO',
            'ALMOXARIFADO',
        ];

        return [
            'name' => $this->faker->randomElement($departments),
        ];
    }
}
