<?php

namespace Database\Factories;

use App\Models\Receipt;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReceiptFactory extends Factory
{
    protected $model = Receipt::class;

    public function definition()
    {
        return [
            'enterprise_id' => null,
            'type_receipt_id' => null,
            'identifier' => null,
        ];
    }
}
