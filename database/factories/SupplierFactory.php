<?php

namespace Database\Factories;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class SupplierFactory extends Factory
{
    protected $model = Supplier::class;

    public function definition()
    {
        return [
            'name' => $this->faker->company(),
            'email' => $this->faker->optional()->companyEmail(),
            'cpf' => null,
            'cnpj' => $this->faker->optional()->numerify('##############'),
            'state_registration' => $this->faker->optional()->numerify('###########'),
            'municipal_registration' => $this->faker->optional()->numerify('###########'),
            'phone' => $this->faker->numerify('###########'),
            'site' => $this->faker->optional()->domainName(),
            'country' => 'Brasil',
            'state' => $this->faker->stateAbbr(),
            'city' => $this->faker->city(),
            'cep' => $this->faker->numerify('########'),
            'neighborhood' => $this->faker->streetName(),
            'address' => $this->faker->streetAddress(),
            'number' => $this->faker->buildingNumber(),
            'complement' => $this->faker->optional()->secondaryAddress(),
            'active' => $this->faker->randomElement([0, 1]),
            'description' => $this->faker->optional()->sentence(8),
        ];
    }
}
