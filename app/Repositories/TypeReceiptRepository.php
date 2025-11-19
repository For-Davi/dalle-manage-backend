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

    public function delete($id)
    {
        $type = $this->findById($id);

        if ($type) {
            return $type->delete();
        }

        return false;
    }
}
