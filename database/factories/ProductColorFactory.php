<?php

namespace Database\Factories;

use App\Models\ProductColor;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductColorFactory extends Factory
{
    protected $model = ProductColor::class;

    public function definition()
    {
        $colors = [
            ['name' => 'VERMELHO', 'hex' => '#FF0000'],
            ['name' => 'AZUL', 'hex' => '#0000FF'],
            ['name' => 'VERDE', 'hex' => '#00FF00'],
            ['name' => 'AMARELO', 'hex' => '#FFFF00'],
            ['name' => 'PRETO', 'hex' => '#000000'],
            ['name' => 'BRANCO', 'hex' => '#FFFFFF'],
            ['name' => 'ROXO', 'hex' => '#800080'],
            ['name' => 'LARANJA', 'hex' => '#FFA500'],
            ['name' => 'CINZA', 'hex' => '#808080'],
            ['name' => 'ROSA', 'hex' => '#FFC0CB'],
        ];

        $color = fake()->randomElement($colors);

        return [
            'name' => $color['name'],
            'hex_color_code' => $color['hex'],
        ];
    }
}
