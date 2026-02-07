<?php

namespace App\Repositories;

use App\Models\ReturnExchangeItem;
use App\Repositories\Base\BaseRepository;

class ReturnExchangeItemRepository extends BaseRepository
{
    public function __construct(ReturnExchangeItem $model)
    {
        parent::__construct($model);
    }

    public function findByReturnId($id)
    {
        return $this->model->where('return_id', $id)->get();
    }
}
