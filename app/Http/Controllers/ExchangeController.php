<?php

namespace App\Http\Controllers;

use App\Http\Requests\Exchange\CreateDifferencePaymentRequest;
use App\Http\Requests\Exchange\CreateExchangePaymentRequest;
use App\Http\Requests\Exchange\IndexExchangeRequest;
use App\Http\Requests\Exchange\ShowExchangeRequest;
use App\Http\Resources\Exchange\ExchangeResource;
use App\Http\Resources\Exchange\ShowExchangeResource;
use App\Repositories\ExchangeRepository;
use App\Services\ExchangeService;
use App\Utils\ErrorLogger;
use Illuminate\Support\Facades\DB;

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

            $exchanges->load(['paymentExchange', 'paymentDifference']);

            return response()->json(['exchanges' => ExchangeResource::collection($exchanges)], 200);
        } catch (\Exception $e) {
            ErrorLogger::log('Erro ao buscar estornos:', $e, $request);

            return response()->json(['message' => 'Erro ao buscar estornos'], 500);
        }
    }

    public function show(ShowExchangeRequest $request)
    {
        try {
            $exchange = $this->repository->findById($request->route('exchangeID'));

            $exchange->load(['paymentExchange', 'paymentDifference', 'additionalExchange']);

            return response()->json(['exchange' => new ShowExchangeResource($exchange)], 200);
        } catch (\Exception $e) {
            ErrorLogger::log('Erro ao buscar estorno:', $e, $request);

            return response()->json(['message' => 'Erro ao buscar estorno'], 500);
        }
    }

    public function createExchangePayment(CreateExchangePaymentRequest $request)
    {
        try {
            DB::beginTransaction();

            $result = $this->service->createExchangePayment($request);

            if ($result) {
                DB::commit();

                $exchanges = $this->repository->getAllBySale($request['additionalExchangePaymentData']['saleID']);

                return response()->json(['exchanges' => ExchangeResource::collection($exchanges)], 201);
            }

        } catch (\Exception $e) {
            DB::rollback();

            ErrorLogger::log('Erro ao criar pagamento do estorno:', $e, $request);

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function createDifferencePayment(CreateDifferencePaymentRequest $request)
    {
        try {
            DB::beginTransaction();

            $result = $this->service->createDifferencePayment($request);

            if ($result) {
                DB::commit();

                $exchanges = $this->repository->getAllBySale($request['additionalDifferencePaymentData']['saleID']);

                return response()->json(['exchanges' => ExchangeResource::collection($exchanges)], 201);
            }

        } catch (\Exception $e) {
            DB::rollback();

            ErrorLogger::log('Erro ao criar pagamento da diferença:', $e, $request);

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
