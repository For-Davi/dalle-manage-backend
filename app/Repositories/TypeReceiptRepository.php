<?php

namespace App\Repositories;

use App\Models\TypeReceipt;

class TypeReceiptRepository
{
    public function __construct(protected TypeReceipt $model) {}

    public function getAllByEnterprise($enterpriseId)
    {
        return $this->model->where('enterprise_id', $enterpriseId)->get();
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
