<?php

namespace App\Http\Controllers;

use App\Services\WebhookAsaasService;
use Illuminate\Http\Request;

class WebhookAsaasController extends BaseController
{
    public function __construct(
        protected WebhookAsaasService $service,
    ) {}

    public function update(Request $request)
    {
        return $this->safeTransaction(function () use ($request) {
            $this->service->update($request);

            return response()->noContent();
        }, 'Erro ao confirmar o pagamento', $request);
    }
}
