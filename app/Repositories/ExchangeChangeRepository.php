<?php

namespace App\Repositories;

use App\Models\ExchangeAdditional;
use App\Repositories\Base\BaseRepository;

class ExchangeChangeRepository extends BaseRepository
{
    public function __construct(ExchangeAdditional $model)
    {
        parent::__construct($model);
    }
}
