<?php

namespace Database\Factories;

use App\Models\Schedule;
use Illuminate\Database\Eloquent\Factories\Factory;

class ScheduleFactory extends Factory
{
    protected $model = Schedule::class;

    public function definition()
    {
        return [
            'date' => $this->faker->date('d-m-Y'),
            'type' => $this->faker->randomElement(['entry', 'out']),
            'value' => $this->faker->randomFloat(2, 1, 10000),
            'transaction_category_id' => null,
            'enterprise_id' => null,
        ];
    }
}
