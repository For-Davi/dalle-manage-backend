<?php

namespace App\Repositories;

use App\Models\SaleItem;
use App\Repositories\Base\BaseRepository;

class SaleItemRepository extends BaseRepository
{
    public function __construct(SaleItem $model)
    {
        parent::__construct($model);
    }

    public function findBySaleId($id)
    {
        return $this->getAllByEnterprise(filters: ['sale_id' => $id]);
    }

    public function updateByProductVariantID(int $productVariantID, int $saleID, array $data)
    {
        $product = $this->model->where('product_variant_id', $productVariantID)->where('sale_id', $saleID)->first();

        if($product){
            $product->update($data);
            return true;
        }

        return null;
    }
}
