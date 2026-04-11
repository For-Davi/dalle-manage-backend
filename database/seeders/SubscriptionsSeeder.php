<?php

namespace Database\Seeders;

use App\Models\Subscription;
use Illuminate\Database\Seeder;

class SubscriptionsSeeder extends Seeder
{
    public function run()
    {
        Subscription::create(['name' => 'free', 'price' => 0]);
        Subscription::create(['name' => 'basic', 'price' => 282]);
        Subscription::create(['name' => 'premium', 'price' => 709]);
    }
}
