<?php

namespace App\Repositories;

use App\Models\ReturnExchangeItem;
use App\Repositories\Base\BaseRepository;

class ReturnExchangeItemRepository extends BaseRepository
{
    public function __construct(ReturnExchangeItem $model)
    {
        parent::__construct($model);
    }

    public function findByReturnId($id, $notDelivered = null)
    {
       if($notDelivered){
            return $this->getAllByEnterprise(filters: ['return_id' => $id, 'delivered' => 0]);
        } else {
            return $this->getAllByEnterprise(filters: ['return_id' => $id]);
        }
    }

    public function updateByProductVariantID(int $productVariantID, int $returnID, array $data)
    {
        $product = $this->model->where('product_variant_id', $productVariantID)->where('return_id', $returnID)->first();

        if($product){
            $product->update($data);
            return true;
        }

        return null;
    }
}
