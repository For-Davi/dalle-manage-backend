<?php

namespace App\Repositories;

use App\Models\TypeReceipt;
use App\Repositories\Base\BaseRepository;

class TypeReceiptRepository extends BaseRepository
{
    public function __construct(TypeReceipt $model)
    {
        parent::__construct($model);
    }

    public function getAllWithFilter($filters)
    {
        $query = $this->model->query();

        if ($filters->active !== null) {
            $query->where('active', $filters->active);
        }

        return $query->get();
    }
}
