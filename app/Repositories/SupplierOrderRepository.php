<?php

namespace App\Repositories;

use App\Models\SupplierOrder;
use App\Repositories\Base\BaseRepository;

class SupplierOrderRepository extends BaseRepository
{
    public function __construct(SupplierOrder $model)
    {
        parent::__construct($model);
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
