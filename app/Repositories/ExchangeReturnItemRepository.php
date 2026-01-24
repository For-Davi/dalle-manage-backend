<?php

namespace App\Repositories;

use App\Models\ExchangeReturnItem;
use App\Repositories\Base\BaseRepository;

class ExchangeReturnItemRepository extends BaseRepository
{
    public function __construct(ExchangeReturnItem $model)
    {
        parent::__construct($model);
    }
}
