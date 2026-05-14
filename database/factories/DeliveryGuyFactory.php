<?php

namespace Database\Factories;

use App\Models\DeliveryGuy;
use Illuminate\Database\Eloquent\Factories\Factory;

class DeliveryGuyFactory extends Factory
{
    protected $model = DeliveryGuy::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->numerify('###########'),
            'cpf' => $this->faker->numerify('###########'),
            'vehicle' => $this->faker->randomElement([
                'Carro', 'Moto', 'Caminhão', 'Van', 'Bicicleta',
            ]).' - '.$this->faker->numerify('####'),
        ];
    }
}
