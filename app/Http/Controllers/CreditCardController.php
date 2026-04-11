<?php

namespace App\Http\Controllers;

use App\Http\Requests\Subscription\Payment\CreditCard\CreatePaymentCreditCardRequest;
use App\Services\CreditCardService;

class CreditCardController extends BaseController
{
    public function __construct(
        private CreditCardService $service,
    ) {}

    public function store(CreatePaymentCreditCardRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            check_permission('subscription.payment');
            $result = $this->service->store($request);

            return response()->json(['result' => $result], 200);
        }, 'Erro ao processar pagamento de cartão de crédito', $request);
    }
}
