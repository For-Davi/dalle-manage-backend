<?php

namespace App\Repositories;

use App\Models\SupplierOrder;

class SupplierOrderRepository
{
    public function __construct(protected SupplierOrder $model) {}

    public function getAllByEnterprise()
    {
        return $this->model->all();
    }

    public function findById($id, $relations = null)
    {
        $query = $this->model;

        if (! empty($relations)) {
            $query = $query->with($relations);
        }

        return $query->find($id);
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

            $order->items->each(function ($item) {
                $item->receivings()->delete();
            });

            $order->status()->delete();
            $order->items()->delete();

            return $order->delete();
        }

        return false;
    }
}
