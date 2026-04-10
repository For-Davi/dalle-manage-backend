<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use App\Models\SaleDelivery;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class DeliveryHelper
{
    public static function existsDelivery($id)
    {
            $delivery = SaleDelivery::where('id', $id)
            ->whereHas('sale', function ($query) {
                $query->where('enterprise_id', Auth::user()->enterprise_id);
            })
            ->exists();

        if (!$delivery) {
            throw ValidationException::withMessages([
            'deliveryID' => ['Esta entrega não pertence a esta empresa.'],
        ]);
        }

        return true;
    }

    public static function isAllZero(array $partialDeliveredData)
    {
            $allAreZero = collect($partialDeliveredData)->every(fn($item) => 
            (int)($item['quantityDelivered'] ?? 0) === 0
        );

        if ($allAreZero) {
            throw ValidationException::withMessages([
                'deliveredProducts.*.quantityDelivered' => ['A quantidade entregue de algum produto deve ser pelo menos 1.'],
            ]);
        }

        return true;
    }

    public static function checkDeliveredAndSaledQuantity($id, array $partialDeliveredData)
    {
            $delivery = SaleDelivery::where('id', $id)
            ->whereHas('sale', function ($query) {
                $query->where('enterprise_id', Auth::user()->enterprise_id);
            })
            ->first();

            foreach($partialDeliveredData as $data){
            $saleProduct = null;

            if($delivery->return_id !== null){
                $saleProduct = DB::table('sale_itens')->where('return_id', $delivery->return_id)->where('product_variant_id', $data['productVariantID'])->first();
            } else {
                $saleProduct = DB::table('sale_itens')->where('sale_id', $delivery->sale_id)->where('product_variant_id', $data['productVariantID'])->first();
            }

            if(!$saleProduct){
                throw ValidationException::withMessages([
                'deliveredProducts.*.productVariantID' => ['O produto informado não existe.'],
                ]);
            }
            if($saleProduct->quantity !== $data['quantitySaled']){
                throw ValidationException::withMessages([
                'deliveredProducts.*.quantityDelivered' => ['A quantidade vendida informada é diferente da quantidade vendida originalmente.'],
                ]);
            }
            if($saleProduct->quantity < $data['quantityDelivered']){
                throw ValidationException::withMessages([
                'deliveredProducts.*.quantityDelivered' => ['A quantidade entregue excede a quantidade vendida.'],
                ]);
            }
            if($saleProduct->quantity_delivered > $data['quantityDelivered']){
                throw ValidationException::withMessages([
                'deliveredProducts.*.quantityDelivered' => ['A quantidade entregue informada não pode ser abaixo da quantidade entregue originalmente.'],
                ]);
            }
            }

            

        if (!$delivery) {
            throw ValidationException::withMessages([
            'deliveryGuyID' => ['Esta entrega não pertence a esta empresa.'],
        ]);
        }

        return true;
    }

    public static function isStatusDelivered(array $partialDeliveredData)
    {
        $allAreDelivered = collect($partialDeliveredData)->every(fn($item) => 
        (int)($item['quantityDelivered'] === $item['quantitySaled'])
        );

        return $allAreDelivered;
    }
}
