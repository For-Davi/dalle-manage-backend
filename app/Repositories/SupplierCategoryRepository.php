<?php

namespace App\Repositories;

use App\Models\SupplierCategory;
use App\Repositories\Base\BaseRepository;
use Illuminate\Support\Facades\DB;

class SupplierCategoryRepository extends BaseRepository
{
    public function __construct(SupplierCategory $model)
    {
        parent::__construct($model);
    }

    public function delete($id)
    {
        $category = $this->findById($id);

        if ($category) {
            DB::table('suppliers')
                ->where('supplier_category_id', $category->id)
                ->update(['supplier_category_id' => null]);

            return $category->delete();
        }

        return false;
    }
}
