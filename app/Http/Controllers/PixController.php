<?php

namespace App\Http\Controllers;

use App\Http\Requests\Subscription\Payment\Pix\CreatePaymentPixRequest;
use App\Http\Resources\Pix\PixResource;
use App\Services\PixService;

class PixController extends BaseController
{
    public function __construct(
        protected PixService $service,
    ) {}

    public function store(CreatePaymentPixRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            check_permission('subscription.payment');
            $pix = $this->service->store($request);

            return response()->json(['pix' => new PixResource($pix['pix'])], 200);
        }, 'Erro ao processar pagamento PIX', $request);
    }
}
