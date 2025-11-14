<?php

namespace App\Repositories;

use App\Models\Sale;

class SaleRepository
{
    public function __construct(protected Sale $model, protected SaleItemRepository $saleItemRepository) {}

    public function getAllByEnterprise($enterpriseId)
    {
        return $this->model
            ->where('enterprise_id', $enterpriseId)->get();
    }

    public function findById($id)
    {
        return $this->model->find($id);
    }

    public function findSaleItensBySaleId($id)
    {
        return $this->saleItemRepository->findBySaleId($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
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
