<?php

namespace Database\Factories;

use App\Models\Enterprise;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

class EnterpriseFactory extends Factory
{
    protected $model = Enterprise::class;

    public function definition()
    {
        $freeSubscription = DB::table('subscriptions')
            ->where('name', 'free')
            ->first();

        return [
            'name' => $this->faker->company(),
            'cnpj' => $this->faker->numerify('##############'),
            'cpf' => null,
            'cep' => $this->faker->numerify('########'),
            'state' => $this->faker->stateAbbr(),
            'city' => $this->faker->city(),
            'neighborhood' => $this->faker->streetName(),
            'address' => $this->faker->streetAddress(),
            'number_address' => $this->faker->buildingNumber(),
            'complement' => $this->faker->optional()->secondaryAddress(),
            'email' => $this->faker->unique()->companyEmail(),
            'phone' => $this->faker->numerify('###########'),
            'subscription_id' => $freeSubscription?->id ?? null,
            'active' => 1,
            'seller_id' => null,
        ];
    }
}
