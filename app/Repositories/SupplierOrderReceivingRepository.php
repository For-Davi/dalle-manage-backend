<?php

namespace App\Repositories;

use App\Models\SupplierOrderReceiving;

class SupplierOrderReceivingRepository
{
    public function __construct(protected SupplierOrderReceiving $model) {}

    public function getAllByEnterprise()
    {
        return $this->model->get();
    }

    public function findById($id)
    {
        return $this->model->find($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $receipt = $this->findById($id);
        if ($receipt) {
            $receipt->update($data);

            return $receipt;
        }

        return null;
    }
}
