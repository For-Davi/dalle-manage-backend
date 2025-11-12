<?php

namespace App\Repositories;

use App\Models\ProductCategory;
use Illuminate\Support\Facades\DB;

class ProductCategoryRepository
{
    public function __construct(protected ProductCategory $model) {}

    public function getAllByEnterprise()
    {
        return $this->model->get();
    }

    public function findById($id)
    {
        return $this->model->find($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $category = $this->findById($id);
        if ($category) {
            $category->update($data);

            return $category;
        }

        return null;
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
