<?php

namespace App\Repositories;

use App\Models\Returns;
use App\Repositories\Base\BaseRepository;

class ReturnRepository extends BaseRepository
{
    public function __construct(Returns $model)
    {
        parent::__construct($model);
    }

    public function getAllBySale($id)
    {
        return $this->model->where('sale_id', $id);
    }
}
