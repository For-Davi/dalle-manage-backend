<?php

namespace App\Repositories;

use App\Models\Exchange;
use App\Repositories\Base\BaseRepository;

class ExchangeRepository extends BaseRepository
{
    public function __construct(Exchange $model)
    {
        parent::__construct($model);
    }

    public function getAllBySale($id)
    {
        return $this->model->where('sale_id', $id)->get();
    }

    public function findByReturnId($id)
    {
        return $this->model->where('return_id', $id)->first();
    }
}
