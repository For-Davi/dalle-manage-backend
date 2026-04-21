<?php

namespace App\Helpers;

use Illuminate\Validation\ValidationException;

class ExchangeHelper
{
    public static function validateExchangeAndDifference($exchangeValue, $differenceValue)
    {
        if ($exchangeValue > 0 && $differenceValue > 0) {
            throw ValidationException::withMessages([
                'exchangeData' => ['O valor do estorno e da diferença ambos não podem ser maiores que 0 simultaneamente.'],
            ]);
        }
        if ($exchangeValue < 0 || $differenceValue < 0) {
            throw ValidationException::withMessages([
                'exchangeData' => ['O valor do estorno e da diferença não podem ser menores que 0.'],
            ]);
        }
    }
}
