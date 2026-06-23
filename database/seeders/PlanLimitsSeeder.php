<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlanLimitsSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'clients' => ['free' => 5, 'basic' => 10, 'premium' => null],
            'users' => ['free' => 1, 'basic' => 5, 'premium' => null],
            'employees' => ['free' => 1, 'basic' => 10, 'premium' => null],
            'suppliers' => ['free' => 1, 'basic' => 10, 'premium' => null],
            'supplier_orders' => ['free' => 0, 'basic' => 0, 'premium' => null],
            'receipts' => ['free' => 2, 'basic' => null, 'premium' => null],
            'movements' => ['free' => 5, 'basic' => 20, 'premium' => null],
            'schedules' => ['free' => 5, 'basic' => 20, 'premium' => null],
            'departments' => ['free' => 1, 'basic' => 10, 'premium' => null],
            'roles' => ['free' => 1, 'basic' => 5, 'premium' => null],
            'grid_groups' => ['free' => 1, 'basic' => 2, 'premium' => null],
            'product_colors' => ['free' => 5, 'basic' => 10, 'premium' => null],
            'tags' => ['free' => 5, 'basic' => 10, 'premium' => null],
            'product_variants' => ['free' => 5, 'basic' => 100, 'premium' => 10000],
            'sales' => ['free' => 50, 'basic' => null, 'premium' => null],
        ];

        foreach ($data as $resource => $plans) {
            foreach ($plans as $planName => $limit) {
                DB::table('plan_limits')->insert([
                    'subscription' => $planName,
                    'resource_key' => $resource,
                    'limit_value' => $limit,
                ]);
            }
        }
    }
}
