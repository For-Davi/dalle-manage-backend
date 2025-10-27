<?php

namespace App\Repositories;

use App\Models\SupplierOrder;

class SupplierOrderRepository
{
    public function __construct(protected SupplierOrder $model) {}

    public function getAll()
    {
        return $this->model->all();
    }

    public function getAllByEnterprise($enterpriseId)
    {
        return $this->model->where('enterprise_id', $enterpriseId)->get();
    }

    public function findById($id)
    {
        return $this->model->find($id);
    }

    public function create($data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $order = $this->findById($id);
        if ($order) {
            $order->update($data);

            return $order;
        }

        return null;
    }

    public function delete($id)
    {
        $order = $this->findById($id);
        if ($order) {

            return $order->delete();
        }

        return false;
    }
}
