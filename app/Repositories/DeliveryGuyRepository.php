<?php

namespace App\Repositories;

use App\Models\DeliveryGuy;
use App\Repositories\Base\BaseRepository;

class DeliveryGuyRepository extends BaseRepository
{
    public function __construct(DeliveryGuy $model)
    {
        parent::__construct($model);
    }

    public function delete($id)
    {
        $deliveryGuy = $this->findById($id);

        if ($deliveryGuy) {
            return $deliveryGuy->delete();
        }

        return false;
    }
}
