<?php

namespace App\Repositories;

use App\Models\ProductAdvanced;
use App\Repositories\Base\BaseRepository;

class ProductAdvancedRepository extends BaseRepository
{
    public function __construct(ProductAdvanced $model)
    {
        parent::__construct($model);
    }
}
