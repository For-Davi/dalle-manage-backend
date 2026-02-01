<?php

namespace App\Http\Controllers;

use App\Repositories\ExchangeRepository;
use App\Services\ExchangeService;
use App\Http\Resources\Exchange\ExchangeResource;
use App\Http\Requests\Exchange\IndexExchangeRequest;
use App\Http\Requests\Exchange\CreatePaymentExchangeRequest;
use Illuminate\Support\Facades\DB;
use App\Utils\ErrorLogger;

class ExchangeController
{
    public function __construct(
        private ExchangeRepository $repository,
        private ExchangeService $service,
    ) {}

    public function index(IndexExchangeRequest $request)
    {
        try {
            $exchanges = $this->repository->getAllBySale($request->route('saleID'));

            return response()->json(['exchanges' => ExchangeResource::collection($exchanges)], 200);
        } catch (\Exception $e) {
            ErrorLogger::log('Erro ao buscar estornos:', $e, $request);

            return response()->json(['message' => 'Erro ao buscar estornos'], 500);
        }
    }

    public function createPayment(CreatePaymentExchangeRequest $request)
    {
        try {
            DB::beginTransaction();

            $result = $this->service->createPayment($request);

            if($result){
                DB::commit();

                $exchanges = $this->repository->getAllBySale($request->route('saleID'));

                return response()->json(['exchanges' => ExchangeResource::collection($exchanges)], 200);
            }

        } catch (\Exception $e) {
            ErrorLogger::log('Erro ao criar pagamento do estorno:', $e, $request);

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
