<?php

namespace App\Http\Controllers;

use App\Http\Requests\Subscription\Payment\CreditCard\CreatePaymentCreditCardRequest;
use App\Services\CreditCardService;
use App\Utils\ErrorLogger;
use Illuminate\Support\Facades\DB;

class CreditCardController
{
    public function __construct(
        private CreditCardService $service,
    ) {}

    public function store(CreatePaymentCreditCardRequest $request)
    {
        try {
            DB::beginTransaction();

            $result = $this->service->store($request);

            if ($result) {
                DB::commit();

                return response()->json(['result' => $result], 200);
            } else {
                return response()->json([
                    'message' => 'Falha ao processar pagamento',
                ], 400);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            dd('esadasd', $e);

            ErrorLogger::log('Erro ao processar pagamento de cartão de crédito:', $e, $request);

            return response()->json(['message' => 'Erro ao processar pagamento de cartão de crédito'], 500);
        }
    }
}
