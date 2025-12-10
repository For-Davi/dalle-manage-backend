<?php

namespace Database\Factories;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'sex' => $this->faker->randomElement(['M', 'F']),
            'cpf' => $this->faker->numerify('###########'),
            'cnpj' => null,
            'date_birthday' => $this->faker->date('d-m-Y'),
            'state_registration' => null,
            'municipal_registration' => null,
            'phone' => $this->faker->numerify('###########'),
            'country' => 'Brasil',
            'state' => $this->faker->stateAbbr(),
            'city' => $this->faker->city(),
            'cep' => $this->faker->numerify('########'),
            'neighborhood' => $this->faker->streetName(),
            'address' => $this->faker->streetAddress(),
            'number' => $this->faker->buildingNumber(),
            'complement' => $this->faker->secondaryAddress(),
            'has_login_access' => 0,
            'description' => $this->faker->sentence(),
        ];
    }
}
