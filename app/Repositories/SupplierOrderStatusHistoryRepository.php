<?php

namespace App\Repositories;

use App\Models\SupplierOrderStatusHistory;
use App\Repositories\Base\BaseRepository;

class SupplierOrderStatusHistoryRepository extends BaseRepository
{
    public function __construct(SupplierOrderStatusHistory $model)
    {
        parent::__construct($model);
    }
}
