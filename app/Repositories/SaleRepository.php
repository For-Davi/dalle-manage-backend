<?php

namespace App\Repositories;

use App\Models\Sale;
use App\Repositories\Base\BaseRepository;

class SaleRepository extends BaseRepository
{
    public function __construct(Sale $model, protected SaleItemRepository $saleItemRepository)
    {
        parent::__construct($model);
    }

    public function findSaleItensBySaleId($id)
    {
        return $this->saleItemRepository->findBySaleId($id);
    }

    public function getCouponInfos($id)
    {
        $products = $this->findSaleItensBySaleId($id);

        $firstProduct = $products[0]->load(['sale', 'sale.client', 'sale.enterprise']);

        return [
            'sale' => $firstProduct->sale,
            'enterprise' => $firstProduct->sale->enterprise,
            'client' => $firstProduct->sale->client,
            'products' => $products,
        ];
    }
}
