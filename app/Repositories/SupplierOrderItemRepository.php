<?php

namespace App\Repositories;

use App\Models\SupplierOrderItem;

class SupplierOrderItemRepository
{
    public function __construct(protected SupplierOrderItem $model) {}

    public function findById($id, $relations = null)
    {
        $query = $this->model;

        if (! empty($relations)) {
            $query = $query->with($relations);
        }

        return $query->find($id);
    }

    public function update($id, array $data)
    {
        $item = $this->findById($id);
        if ($item) {
            $item->update($data);

            return $item;
        }

        return null;
    }
}
