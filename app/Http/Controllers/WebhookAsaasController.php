<?php

namespace App\Http\Controllers;

use App\Services\WebhookAsaasService;
use App\Utils\ErrorLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WebhookAsaasController
{
    public function __construct(
        protected WebhookAsaasService $service,
    ) {}

    public function update(Request $request)
    {
        try {
            DB::beginTransaction();

            \Log::info(['dados q chegaram do payments' => $request->all()]);

            $result = $this->service->update($request);

            if ($result) {
                DB::commit();

                return response()->noContent();
            }
        } catch (\Exception $e) {
            DB::rollBack();

            ErrorLogger::log('Erro ao confirmar o pagamento:', $e, $request);

            return response()->json(['message' => 'Erro ao confirmar o pagamento'], 500);
        }
    }
}
