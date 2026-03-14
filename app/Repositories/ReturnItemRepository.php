<?php

namespace App\Repositories;

use App\Models\ReturnItem;
use App\Repositories\Base\BaseRepository;

class ReturnItemRepository extends BaseRepository
{
    public function __construct(ReturnItem $model)
    {
        parent::__construct($model);
    }

    public function findByReturnId($id)
    {
        return $this->model->where('return_id', $id)->get();
    }

    public function findByReturnAndProductVariantId($returnID, $productVariantID)
    {
        return $this->model->where('return_id', $returnID)->where('product_variant_id', $productVariantID)->first();
    }
}
