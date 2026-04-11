<?php

namespace Database\Factories;

use App\Models\GridGroup;
use App\Models\GridItem;
use App\Models\Product;
use App\Models\ProductAdvanced;
use App\Models\ProductCategory;
use App\Models\ProductColor;
use App\Models\ProductLog;
use App\Models\ProductTag;
use App\Models\ProductVariant;
use App\Models\Tag;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'name' => null,
            'type' => 'product',
            'enterprise_id' => null,
            'product_category_id' => null,
            'description' => $this->faker->sentence,
        ];
    }

    public function withRandomCategory()
    {
        return $this->afterMaking(function (Product $product) {
            if ($product->enterprise_id) {
                $product->product_category_id = ProductCategory::where('enterprise_id', $product->enterprise_id)
                    ->inRandomOrder()
                    ->value('id');
            }
        });
    }

    public function withRandomTags()
    {
        return $this->afterCreating(function (Product $product) {
            $tagIds = Tag::where('enterprise_id', $product->enterprise_id)->pluck('id');
            if ($tagIds->isNotEmpty()) {
                $amount = rand(0, $tagIds->count());
                if ($amount > 0) {
                    $randomTags = $tagIds->random($amount);
                    $data = $randomTags->map(fn ($tagId) => [
                        'product_id' => $product->id,
                        'tag_id' => $tagId,
                    ])->toArray();
                    ProductTag::insert($data);
                }
            }
        });
    }

    public function withAdvancedSettings()
    {
        return $this->afterCreating(function (Product $product) {
            ProductAdvanced::create([
                'product_id' => $product->id,
                'active' => 1,
                'allow_coupon' => 1,
                'allow_discount' => 1,
                'discount_max_percentage' => 10,
                'has_commission' => 1,
                'commission_percentage' => 5,
            ]);
        });
    }

    public function withLog()
    {
        return $this->afterCreating(function (Product $product) {
            $user = User::where('enterprise_id', $product->enterprise_id)
                ->whereHas('role', fn ($q) => $q->where('name', 'Master'))
                ->first();

            $userName = $user ? $user->name : 'Sistema';
            $userEmail = $user ? $user->email : 'N/A';
            $date = Carbon::now()->locale('pt_BR')->translatedFormat('d/m/Y H:i:s');

            ProductLog::create([
                'product_id' => $product->id,
                'execution' => 'create',
                'description' => "O usuário(a) {$userName} ({$userEmail}) criou este produto em {$date}",
            ]);
        });
    }

    public function withVariants()
    {
        return $this->afterCreating(function (Product $product) {

            $colors = ProductColor::where('enterprise_id', $product->enterprise_id)->get();

            if ($colors->isEmpty()) {
                return;
            }

            $gridGroup = GridGroup::where('enterprise_id', $product->enterprise_id)
                ->inRandomOrder()
                ->first();

            if (! $gridGroup) {
                foreach ($colors as $color) {
                    $this->createVariant($product, $color, null);
                }

                return;
            }

            $gridItems = GridItem::where('grid_group_id', $gridGroup->id)->get();

            if ($gridItems->isEmpty()) {
                return;
            }

            foreach ($gridItems as $item) {
                foreach ($colors as $color) {
                    $this->createVariant($product, $color, $item->id);
                }
            }
        });
    }

    private function createVariant(Product $product, $color = null, $gridItemId = null)
    {
        return ProductVariant::create([
            'product_id' => $product->id,
            'enterprise_id' => $product->enterprise_id,
            'color_id' => $color?->id,
            'grid_item_id' => $gridItemId,
            'price' => $this->faker->randomFloat(2, 10, 200),
            'cost' => $this->faker->randomFloat(2, 5, 100),
            'stock_quantity' => rand(0, 100),
            'min_stock_alert' => rand(0, 10),
            'active' => 1,
        ]);
    }
}
