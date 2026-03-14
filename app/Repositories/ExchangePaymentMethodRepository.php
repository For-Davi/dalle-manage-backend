<?php

namespace App\Repositories;

use App\Models\ExchangePaymentMethod;
use App\Repositories\Base\BaseRepository;

class ExchangePaymentMethodRepository extends BaseRepository
{
    public function __construct(ExchangePaymentMethod $model)
    {
        parent::__construct($model);
    }
}
