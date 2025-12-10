<?php

namespace Database\Factories;

use App\Models\GridGroup;
use App\Models\GridItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class GridGroupFactory extends Factory
{
    protected $model = GridGroup::class;

    public function definition(): array
    {
        return [
            'name' => null,
            'active' => 1,
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (GridGroup $group) {

            $itemsByGroup = [
                'LETRAS' => ['PP', 'P', 'M', 'G', 'GG'],
                'NÚMEROS' => ['38', '40', '42', '44', '46'],
                'TAMANHO ÚNICO' => ['Único'],
            ];

            if (! isset($itemsByGroup[$group->name])) {
                return;
            }

            foreach ($itemsByGroup[$group->name] as $index => $size) {
                GridItem::create([
                    'size' => $size,
                    'order' => $index + 1,
                    'active' => true,
                    'grid_group_id' => $group->id,
                    'enterprise_id' => $group->enterprise_id,
                ]);
            }
        });
    }
}
