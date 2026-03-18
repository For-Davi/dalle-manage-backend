<?php

namespace App\Http\Controllers;

use App\DTO\Sale\FilterSaleDTO;
use App\Http\Requests\Sale\CreateSaleRequest;
use App\Http\Requests\Sale\DeleteSaleRequest;
use App\Http\Requests\Sale\ExportSaleRequest;
use App\Http\Requests\Sale\FilterSaleRequest;
use App\Http\Requests\Sale\SendToEmailRequest;
use App\Http\Requests\Sale\ShowCancellationRequest;
use App\Http\Requests\Sale\ShowSaleRequest;
use App\Http\Requests\Sale\UpdateSaleRequest;
use App\Http\Resources\Sale\SaleCancellationResource;
use App\Http\Resources\Sale\SaleItensResource;
use App\Http\Resources\Sale\SaleResource;
use App\Http\Resources\Sale\SalesIndexResource;
use App\Repositories\SaleCancellationRepository;
use App\Repositories\SaleItemRepository;
use App\Repositories\SaleRepository;
use App\Services\SaleService;
use Illuminate\Http\Request;

class SaleController extends BaseController
{
    public function __construct(
        private SaleService $service,
        private SaleRepository $repository,
        private SaleItemRepository $saleItemRepository,
        private SaleCancellationRepository $saleCancellationRepository,
    ) {}

    public function index(Request $request)
    {
        return $this->safeExecute(function () {
            $sales = $this->repository->getAllByEnterprise();

            return response()->json(['sales' => SalesIndexResource::collection($sales)], 200);
        }, 'Erro ao fazer busca de vendas', $request);
    }

    public function filter(FilterSaleRequest $request)
    {
        return $this->safeExecute(function () use ($request) {
            $saleFilterDTO = FilterSaleDTO::fromRequest($request);
            $sales = $this->repository->getAllWithFilter($saleFilterDTO->toArray());

            return response()->json(['sales' => SalesIndexResource::collection($sales)], 200);
        }, 'Erro ao filtrar vendas', $request);
    }

    public function show(ShowSaleRequest $request)
    {
        return $this->safeExecute(function () use ($request) {
            $sale = $this->repository->findById($request->route('saleID'));
            $sale->load(['delivery', 'paymentWithCredit.type', 'items.product.color']);

            return response()->json(['sale' => new SaleResource($sale)], 200);
        }, 'Erro ao buscar venda', $request);
    }

    public function store(CreateSaleRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            $sale = $this->service->create($request);

            return response()->json(['sale' => $sale, 'message' => 'Venda realizada'], 201);
        }, 'Erro ao fazer a venda', $request);
    }

    public function update(UpdateSaleRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            $this->service->update($request);

            return response()->json(['message' => 'Venda cancelada'], 201);
        }, 'Erro ao cancelar a venda', $request);
    }

    public function destroy(DeleteSaleRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            $this->repository->deleteSale($request->route('saleID'));
            $sales = $this->repository->getAllByEnterprise();

            return response()->json(['sales' => SalesIndexResource::collection($sales), 'message' => 'Venda excluída'], 200);
        }, 'Erro ao excluir venda', $request);
    }

    public function showProducts(ShowSaleRequest $request)
    {
        return $this->safeExecute(function () use ($request) {
            $products = $this->saleItemRepository->findBySaleId($request->route('saleID'));
            $products->load(['product.color']);

            return response()->json(['saleItens' => SaleItensResource::collection($products)], 200);
        }, 'Erro ao buscar produtos da venda', $request);
    }

    public function showCancellation(ShowCancellationRequest $request)
    {
        return $this->safeExecute(function () use ($request) {
            $cancellation = $this->saleCancellationRepository->findBySaleId($request->route('saleID'));

            return response()->json(['cancellation' => new SaleCancellationResource($cancellation)], 200);
        }, 'Erro ao buscar cancelamento da venda', $request);
    }

    public function showCouponInfos(ShowSaleRequest $request)
    {
        return $this->safeExecute(function () use ($request) {
            $couponData = $this->repository->getCouponInfos($request->route('saleID'));

            return response()->json(['couponData' => $couponData], 200);
        }, 'Erro ao localizar os itens da venda', $request);
    }

    public function export(ExportSaleRequest $request)
    {
        return $this->safeExecute(function () use ($request) {
            return $this->service->export($request);
        }, 'Erro ao exportar venda', $request);
    }

    public function sendToEmail(SendToEmailRequest $request)
    {
        return $this->safeExecute(function () use ($request) {
            $result = $this->service->sendToEmail($request);

            return response()->json(['message' => $result], 200);
        }, 'Erro ao enviar cupom para o email', $request);
    }
}
