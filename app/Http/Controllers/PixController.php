<?php

namespace App\Http\Controllers;

use App\Http\Requests\Subscription\Payment\Pix\CreatePaymentPixRequest;
use App\Http\Resources\Pix\PixResource;
use App\Services\PixService;
use App\Utils\ErrorLogger;
use Illuminate\Support\Facades\DB;

class PixController
{
    public function __construct(
        protected PixService $service,
    ) {}

    public function store(CreatePaymentPixRequest $request)
    {
        try {
            DB::beginTransaction();

            $pix = $this->service->store($request);

            if ($pix) {
                DB::commit();

                return response()->json(['pix' => new PixResource($pix['pix'])], 200);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            ErrorLogger::log('Erro ao processar pagamento PIX:', $e, $request);

            return response()->json(['message' => 'Erro ao processar pagamento PIX'], 500);
        }
    }
}
