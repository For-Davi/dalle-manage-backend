<?php

namespace App\Repositories;

use App\Models\ProductMovement;
use App\Repositories\Base\BaseRepository;

class ProductMovementRepository extends BaseRepository
{
    public function __construct(ProductMovement $model)
    {
        parent::__construct($model);
    }

    public function delete($id)
    {
        $movement = $this->findById($id);
        if ($movement) {
            return $movement->delete();
        }

        return false;
    }
}
