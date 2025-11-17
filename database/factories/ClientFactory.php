<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClientFactory extends Factory
{
    protected $model = Client::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'sex' => $this->faker->randomElement(['M', 'F']),
            'phone' => $this->faker->numerify('###########'),
            'cpf' => $this->faker->numerify('###########'),
            'cnpj' => null,
            'state_registration' => $this->faker->optional()->numerify('###########'),
            'municipal_registration' => $this->faker->optional()->numerify('###########'),
            'date_birthday' => $this->faker->date('d-m-Y'),
            'cep' => $this->faker->numerify('########'),
            'country' => 'Brasil',
            'state' => $this->faker->stateAbbr(),
            'city' => $this->faker->city(),
            'neighborhood' => $this->faker->streetName(),
            'address' => $this->faker->streetAddress(),
            'complement' => $this->faker->optional()->secondaryAddress(),
            'number' => $this->faker->buildingNumber(),
            'description' => $this->faker->sentence(),
        ];
    }
}
