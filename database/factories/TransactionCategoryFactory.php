<?php

namespace Database\Factories;

use App\Models\TransactionCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionCategoryFactory extends Factory
{
    protected $model = TransactionCategory::class;

    public function definition()
    {
        $categories = [
            'Aluguel',
            'Energia',
            'Água',
            'Internet',
            'Combustível',
            'Gás',
            'Manutenção',
            'Limpeza',
            'Material de Escritório',
            'Construção',
            'Marketing',
            'Transporte',
            'Impostos',
            'Segurança',
            'Salários',
            'Licenças de Software',
            'Telefone',
            'Compras',
        ];

        return [
            'name' => $this->faker->randomElement($categories),
        ];
    }
}
