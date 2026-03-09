<?php

namespace App\Repositories;

use App\Models\ExchangeAdditional;
use App\Repositories\Base\BaseRepository;

class ExchangeAdditionalRepository extends BaseRepository
{
    public function __construct(ExchangeAdditional $model)
    {
        parent::__construct($model);
    }

    public function findByExchangeId($id)
    {
        return $this->getAllByEnterprise(filters: ['exchange_id' => $id])->first();
    }
}
