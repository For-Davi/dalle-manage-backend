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

        foreach($enterprises as $enterprise){
            $types = [
                'Dinheiro',
                'Cartão de crédito',
                'Cartão de débito',
                'PIX',
            ]
        }
        foreach ($types as $type) {
                TypeReceipt::create([
                    'name' => $type,
                    'enterprise_id' => $enterprise->id,
                ]);
            }
    }
}
