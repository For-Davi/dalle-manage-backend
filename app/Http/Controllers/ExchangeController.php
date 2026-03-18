<?php

namespace App\Http\Controllers;

use App\Http\Requests\Exchange\CreateDifferencePaymentRequest;
use App\Http\Requests\Exchange\CreateExchangePaymentRequest;
use App\Http\Requests\Exchange\ExportExchangeRequest;
use App\Http\Requests\Exchange\IndexExchangeRequest;
use App\Http\Requests\Exchange\SendToEmailRequest;
use App\Http\Requests\Exchange\ShowExchangeRequest;
use App\Http\Resources\Exchange\ExchangeResource;
use App\Http\Resources\Exchange\ExchangeTaxCouponResource;
use App\Http\Resources\Exchange\ShowExchangeResource;
use App\Repositories\ExchangeRepository;
use App\Services\ExchangeService;

class ExchangeController extends BaseController
{
    public function __construct(
        private ExchangeRepository $repository,
        private ExchangeService $service,
    ) {}

    public function index(IndexExchangeRequest $request)
    {
        return $this->safeExecute(function () use ($request) {
            $exchanges = $this->repository->getAllBySale($request->route('saleID'));
            $exchanges->load(['paymentExchange', 'paymentDifference']);

            return response()->json(['exchanges' => ExchangeResource::collection($exchanges)], 200);
        }, 'Erro ao buscar estornos', $request);
    }

    public function show(ShowExchangeRequest $request)
    {
        return $this->safeExecute(function () use ($request) {
            $exchange = $this->repository->findById($request->route('exchangeID'));
            $exchange->load(['paymentExchange', 'paymentDifference', 'additionalExchange']);

            return response()->json(['exchange' => new ShowExchangeResource($exchange)], 200);
        }, 'Erro ao buscar estorno', $request);
    }

    public function createExchangePayment(CreateExchangePaymentRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            $this->service->createExchangePayment($request);
            $exchanges = $this->repository->getAllBySale($request['additionalExchangePaymentData']['saleID']);

            return response()->json(['exchanges' => ExchangeResource::collection($exchanges), 'message' => 'Pagamento do estorno realizado'], 201);
        }, 'Erro ao criar pagamento do estorno', $request);
    }

    public function createDifferencePayment(CreateDifferencePaymentRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            $this->service->createDifferencePayment($request);
            $exchange = $this->repository->findById($request['additionalDifferencePaymentData']['exchangeID'], ['additionalExchange', 'return.returnExchangeItems', 'sale.enterprise']);

            return response()->json(['exchangeTaxCoupon' => new ExchangeTaxCouponResource($exchange), 'message' => 'Pagamento da diferença realizado'], 201);
        }, 'Erro ao criar pagamento da diferença', $request);
    }

    public function export(ExportExchangeRequest $request)
    {
        return $this->safeExecute(function () use ($request) {
            return $this->service->export($request);
        }, 'Erro ao exportar troca', $request);
    }

    public function sendToEmail(SendToEmailRequest $request)
    {
        return $this->safeExecute(function () use ($request) {
            $result = $this->service->sendToEmail($request);

            return response()->json(['message' => $result], 200);
        }, 'Erro ao enviar cupom para o email', $request);
    }
}
