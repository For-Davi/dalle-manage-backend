<?php

namespace App\Repositories;

use App\Models\SupplierOrderReceiving;
use App\Repositories\Base\BaseRepository;

class SupplierOrderReceivingRepository extends BaseRepository
{
    public function __construct(SupplierOrderReceiving $model)
    {
        parent::__construct($model);
    }
}
