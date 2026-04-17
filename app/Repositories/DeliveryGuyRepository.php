<?php

namespace App\Repositories;

use App\Models\DeliveryGuy;
use App\Repositories\Base\BaseRepository;
use Illuminate\Support\Facades\DB;

class DeliveryGuyRepository extends BaseRepository
{
    public function __construct(DeliveryGuy $model)
    {
        parent::__construct($model);
    }

    public function delete($id)
    {
        $deliveryGuy = $this->findById($id);

        if($deliveryGuy){
            DB::table('sale_deliveries')->where('delivery_guy_id', $id)->update(['delivery_guy_id' => null]);;
            return $deliveryGuy->delete();
        }

        return false;
    }
}
