<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ExchangePaymentHelper
{
    public static function findPaymentMethodID($name, $enterpriseId, $receiptId)
    {
        $existPaymentMethod = DB::table('types_receipt')->where('enterprise_id', $enterpriseId)->where('name', $name)->first();

        if ($existPaymentMethod) {

            if ($existPaymentMethod->name !== 'CREDIT') {
                $isTypePaymentReceiptCorrect = DB::table('receipts')->where('id', $receiptId)
                    ->where('type_receipt_id', $existPaymentMethod->id)
                    ->first();

                if ($isTypePaymentReceiptCorrect) {
                    return $existPaymentMethod->id;
                } else {
                    throw ValidationException::withMessages([
                        'paymentData.payment.*.receiptID' => ['O tipo de pagamento informado não condiz com o tipo do recebimento.'],
                    ]);
                }
            }

            return $existPaymentMethod->id;
        } else {
            throw ValidationException::withMessages([
                'exchangeData.*.paymentType' => ['O tipo de pagamento informado não existe.'],
            ]);
        }
    }

    public static function existsReceipt($receiptId)
    {
        $existReceipt = DB::table('receipts')->where('id', $receiptId)->first();

        if (! $existReceipt) {
            throw ValidationException::withMessages([
                'payment.*.receiptID' => ['O tipo de recebimento informado não existe.'],
            ]);
        }
        if ($existReceipt && $existReceipt->active === 0) {
            throw ValidationException::withMessages([
                'payment.*.receiptID' => ['O tipo de recebimento informado não está ativo.'],
            ]);
        }
    }
}
