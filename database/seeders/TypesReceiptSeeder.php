<?php

namespace Database\Seeders;

use App\Models\Enterprise;
use App\Models\TypeReceipt;
use Illuminate\Database\Seeder;

class TypesReceiptSeeder extends Seeder
{
    public function run()
    {
        $enterprises = Enterprise::all();

        foreach ($enterprises as $enterprise) {
            $types = [
                'Dinheiro',
                'Cartão de crédito',
                'Cartão de débito',
                'PIX',
                'Transferência Bancária',
            ];
        }
        foreach ($types as $type) {
            TypeReceipt::create([
                'name' => $type,
                'enterprise_id' => $enterprise->id,
            ]);
        }
    }
}
