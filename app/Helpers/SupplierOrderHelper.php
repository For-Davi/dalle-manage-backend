<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SupplierOrderHelper
{
    public static function verifyQuantityReceived($itemID, $received)
    {
        $item = DB::table('supplier_order_items')
            ->where('id', $itemID)
            ->first();

        if ($item) {
            $quantityRequested = floatval($item->quantity_requested);
            $quantityReceived = floatval($item->quantity_received);
            $received = floatval($received);

            $remaining = $quantityRequested - $quantityReceived;

            if ($received > $remaining) {
                throw ValidationException::withMessages([
                    'received' => ['A quantidade informada de recebimento de um item ultrapassa o que ainda falta a receber'],
                ]);
            }
        }
    }
}
