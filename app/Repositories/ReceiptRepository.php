<?php

namespace App\Repositories;

use App\DTO\Receipt\FilterReceiptDTO;
use App\Models\Receipt;
use App\Repositories\Base\BaseRepository;

class ReceiptRepository extends BaseRepository
{
    public function __construct(Receipt $model)
    {
        parent::__construct($model);
    }

    public function getAllWithFilter(FilterReceiptDTO $filters)
    {
        $query = $this->model->where('enterprise_id', $filters->enterpriseID);

        if ($filters->active !== null) {
            $query->where('active', $filters->active)->with('type');
        }

        return $query->get();
    }

    public function delete($id)
    {
        $receipt = $this->findById($id);

        if ($receipt) {
            return $receipt->delete();
        }

        return false;
    }
}
