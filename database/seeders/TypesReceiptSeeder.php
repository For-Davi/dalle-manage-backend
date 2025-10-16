<?php

namespace Database\Seeders;

use App\Models\TypeReceipt;
use Illuminate\Database\Seeder;
use App\Models\Enterprise;

class TypesReceiptSeeder extends Seeder
{
    public function run()
    {
        $enterprises = Enterprise::all();

        $types = [
            'PIX',
            'CREDIT_CARD',
            'DEBT_CARD',
            'MONEY',
        ];

        foreach($enterprises as $enterprise){
            foreach ($types as $type) {
                    TypeReceipt::create([
                        'name' => $type,
                        'enterprise_id' => $enterprise->id,
                    ]);
                }
        }
    }
}
