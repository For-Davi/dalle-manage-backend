<?php

namespace Database\Seeders;

use App\Models\Enterprise;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingSystemSeeder extends Seeder
{
    public function run(): void
    {
        $enterprises = Enterprise::all();

        foreach ($enterprises as $enterprise) {
            DB::table('setting_system')->insert([
                'enterprise_id' => $enterprise->id,
                'send_notification_stock_critical' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
