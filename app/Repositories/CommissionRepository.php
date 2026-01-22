<?php

namespace App\Repositories;

use App\Models\Commission;
use App\Repositories\Base\BaseRepository;

class CommissionRepository extends BaseRepository
{
    public function __construct(Commission $model)
    {
        parent::__construct($model);
    }

    public function getAllBySale($saleID)
    {
         return $this->model->where('sale_id', $saleID)->get();
    }
}
