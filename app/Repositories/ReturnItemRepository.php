<?php

namespace App\Repositories;

use App\Models\ReturnItem;
use App\Repositories\Base\BaseRepository;

class ReturnItemRepository extends BaseRepository
{
    public function __construct(ReturnItem $model)
    {
        parent::__construct($model);
    }
}
