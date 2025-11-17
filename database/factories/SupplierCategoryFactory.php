<?php

namespace Database\Factories;

use App\Models\SupplierCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class SupplierCategoryFactory extends Factory
{
    protected $model = SupplierCategory::class;

    public function definition()
    {
        $categories = [
            'Alimentação',
            'Limpeza',
            'Construção',
            'Tecnologia',
            'Escritório',
            'Transporte',
            'Manutenção',
            'Equipamentos',
            'Serviços Gerais',
            'Vestuário',
            'Material Escolar',
            'Higiene',
            'Farmácia e Saúde',
            'Ferramentas',
            'Automotivo',
        ];

        return [
            'name' => $this->faker->randomElement($categories),
        ];
    }
}
