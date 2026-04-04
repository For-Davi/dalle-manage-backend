<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DeliveryGuyHelper
{
    public static function existsDeliveryGuy($id)
    {
            if($id){
                $deliveryGuy = DB::table('delivery_guys')
                ->where('id', $id)
                ->where('enterprise_id', Auth::user()->enterprise_id)
                ->first();

            if ($deliveryGuy) {
                return true;
            } else {
                throw ValidationException::withMessages([
                    'deliveryGuyID' => ['O entregador informado não possui cadastro.'],
                ]);
            }
            }
    }
}
