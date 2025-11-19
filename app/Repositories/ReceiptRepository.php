<?php

namespace App\Repositories;

use App\Models\Receipts;
use App\Repositories\Base\BaseRepository;

class ReceiptRepository extends BaseRepository
{
    public function __construct(Receipts $model)
    {
        parent::__construct($model);
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
