<?php

namespace Database\Factories;

use App\Models\TypeReceipt;
use Illuminate\Database\Eloquent\Factories\Factory;

class TypeReceiptFactory extends Factory
{
    protected $model = TypeReceipt::class;

    public function definition()
    {
        return [
            'name' => null,
            'enterprise_id' => null,
        ];
    }
}
