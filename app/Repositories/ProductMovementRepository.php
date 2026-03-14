<?php

namespace App\Repositories;

use App\Models\ProductMovement;
use App\Repositories\Base\BaseRepository;

class ProductMovementRepository extends BaseRepository
{
    protected $productVariantRepository;

    public function __construct(ProductMovement $model, ProductVariantRepository $productVariantRepository)
    {
        parent::__construct($model);
        $this->productVariantRepository = $productVariantRepository;
    }

    public function updateTradeProductsMovement($id, $data, $type)
    {
        $movements = $type === 'sale' ? $this->getAllByEnterprise(filters: ['sale_id' => $id]) : $this->getAllByEnterprise(filters: ['return_id' => $id]);

        foreach ($movements as $movement) {
            $movement->update($data);
            $product = $this->productVariantRepository->findById($movement->id);

            if ($data['status'] === 'active') {
                $newStock = $product->stock_quantity - $movement->quantity;
                $product->update(['stock_quantity' => $newStock]);
            } else {
                $newStock = $product->stock_quantity + $movement->quantity;
                $product->update(['stock_quantity' => $newStock]);
            }
        }

        return true;
    }

    public function delete($id)
    {
        $movement = $this->findById($id);
        if ($movement) {
            return $movement->delete();
        }

        return false;
    }
}
