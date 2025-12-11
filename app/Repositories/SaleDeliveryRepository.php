<?php

namespace App\Repositories;

use App\Models\SaleDelivery;
use App\Repositories\Base\BaseRepository;

class SaleDeliveryRepository extends BaseRepository
{
    public function __construct(SaleDelivery $model)
    {
        parent::__construct($model);
    }
}
