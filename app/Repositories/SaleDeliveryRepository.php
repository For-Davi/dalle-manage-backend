<?php

namespace App\Repositories;

use App\Models\SaleDelivery;

class SaleDeliveryRepository
{
    public function __construct(protected SaleDelivery $model) {}

    public function getAllByEnterprise($enterpriseId)
    {
        return $this->model
            ->where('enterprise_id', $enterpriseId)->get();
    }

    public function findById($id)
    {
        return $this->model->find($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }
}
