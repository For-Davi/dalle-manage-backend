<?php

namespace App\Repositories;

use App\Models\SalePaymentMethod;
use App\Repositories\Base\BaseRepository;

class SalePaymentsMethodRepository extends BaseRepository
{
    public function __construct(SalePaymentMethod $model)
    {
        parent::__construct($model);
    }
}
