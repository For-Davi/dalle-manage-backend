<?php

namespace App\Repositories;

use App\Models\SupplierCatalog;
use App\Repositories\Base\BaseRepository;

class SupplierCatalogRepository extends BaseRepository
{
    public function __construct(SupplierCatalog $model)
    {
        parent::__construct($model);
    }

    public function updateBySupplierAndVariant(int $supplierId, int $productVariantId, array $data)
    {
        $item = $this->model
            ->where('supplier_id', $supplierId)
            ->where('product_variant_id', $productVariantId)
            ->first();

        if ($item) {
            $item->update($data);

            return $item;
        }

        return null;
    }

    public function delete($supplierId, $productVariantId)
    {
        $item = $this->model
            ->where('supplier_id', $supplierId)
            ->where('product_variant_id', $productVariantId)
            ->first();

        if ($item) {
            return $item->delete();
        }

        return false;
    }

    public function getBySupplier($supplierId, ?array $relations = null)
    {
        $query = $this->model->where('supplier_id', $supplierId);

        if (! empty($relations)) {
            $query->with($relations);
        }

        return $query->get();
    }

    public function getByVariant(int $variantID, array $relations = [])
    {
        return $this->model
            ->with($relations)
            ->where('product_variant_id', $variantID)
            ->get();
    }
}
