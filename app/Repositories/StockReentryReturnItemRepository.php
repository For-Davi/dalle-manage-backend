<?php

namespace App\Repositories;

use App\Models\StockReentryReturnItem;
use App\Repositories\Base\BaseRepository;

class StockReentryReturnItemRepository extends BaseRepository
{
    protected $returnItemRepository;

    public function __construct(StockReentryReturnItem $model, ReturnItemRepository $returnItemRepository)
    {
        parent::__construct($model);
        $this->returnItemRepository = $returnItemRepository;
    }

    public function findByProductVariantId($id)
    {
        return $this->getAllByEnterprise(filters: ['product_variant_id' => $id]);
    }

    public function updateStockReentryReturnItem($id, $data)
    {
        $product = $this->findByProductVariantId($id);

        if ($product) {
            $product->update($data);

            return $product;
        }

        return null;
    }

    public function updateStockReentry($productVariantID, $quantity, $status, $enterpriseID, $returnID)
    {
        $product = $this->findByProductVariantId($productVariantID);

        if ($status === 'active') {

            if ($product) {
                return $product->update(['quantity' => $product->quantity + $quantity]);
            }
            $returnItem = $this->returnItemRepository->findByReturnAndProductVariantId($returnID, $productVariantID);

            if ($returnItem) {

                $stockReentryData = [
                    'product_variant_id' => $returnItem->product_variant_id,
                    'enterprise_id' => $enterpriseID,
                    'product_name' => $returnItem->product_name,
                    'product_sku' => $returnItem->product_sku,
                    'product_code' => $returnItem->product_code,
                    'product_color' => $returnItem->product_color,
                    'product_color_name' => $returnItem->product_color_name,
                    'quantity' => $returnItem->quantity,
                ];

                return $this->create($stockReentryData);
            }

            return false;

        } else {

            if ($product) {
                $product->update(['quantity' => $product->quantity - $quantity]);

                if ($product->quantity === 0) {
                    return $product->delete();
                }

            }

            return true;
        }

        return false;
    }
}
