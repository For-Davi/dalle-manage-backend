<?php

namespace App\Repositories;

use App\Models\SaleItem;

class SaleItemRepository
{
    public function __construct(protected SaleItem $model) {}

    public function getAllByEnterprise($enterpriseId)
    {
        return $this->model
            ->where('enterprise_id', $enterpriseId)->get();
    }

    public function findById($id)
    {
        return $this->model->find($id);
    }

    public function findBySaleId($id)
    {
        return $this->model->where('sale_id', $id)->get();
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }
}
