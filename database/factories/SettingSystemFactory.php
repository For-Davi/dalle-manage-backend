<?php

namespace Database\Factories;

use App\Models\SettingSystem;
use Illuminate\Database\Eloquent\Factories\Factory;

class SettingSystemFactory extends Factory
{
    protected $model = SettingSystem::class;

    public function definition()
    {
        return [
            'send_notification_stock_critical' => $this->faker->randomElement([0, 1]),
        ];
    }
}
