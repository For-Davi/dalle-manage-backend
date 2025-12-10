<?php

namespace App\Repositories;

use App\Models\SupplierOrderItem;
use App\Repositories\Base\BaseRepository;

class SupplierOrderItemRepository extends BaseRepository
{
    public function __construct(SupplierOrderItem $model)
    {
        parent::__construct($model);
    }
}
