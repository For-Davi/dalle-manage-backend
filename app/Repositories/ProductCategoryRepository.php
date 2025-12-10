<?php

namespace App\Repositories;

use App\Models\ProductCategory;
use App\Repositories\Base\BaseRepository;
use Illuminate\Support\Facades\DB;

class ProductCategoryRepository extends BaseRepository
{
    public function __construct(ProductCategory $model)
    {
        parent::__construct($model);
    }

    public function delete($id)
    {
        $category = $this->findById($id);

        if ($category) {
            DB::table('products')
                ->where('enterprise_id', $category->enterprise_id)
                ->where('product_category_id', $category->id)
                ->update(['product_category_id' => null]);

            return $category->delete();
        }

        return false;
    }
}
