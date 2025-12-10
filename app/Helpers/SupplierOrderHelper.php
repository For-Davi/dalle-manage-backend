<?php

namespace App\Helpers;

use App\Enums\Supplier\Order\SupplierOrderStatus;
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

    public static function verifyStatusAndRoles($status, $orderID)
    {
        if ($status === SupplierOrderStatus::PARTIAL_FINISHED->value) {

            $items = DB::table('supplier_order_items')
                ->select('quantity_received')
                ->where('supplier_order_id', $orderID)
                ->get();

            $noneReceived = $items->every(function ($item) {
                return intval($item->quantity_received) === 0;
            });

            if ($noneReceived) {
                throw ValidationException::withMessages([
                    'received' => ['Não é possível finalizar parcialmente o pedido, pois nenhum item foi recebido.'],
                ]);
            }
        }
    }
}
