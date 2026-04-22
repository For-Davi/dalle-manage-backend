<?php

namespace App\Http\Controllers;

use App\Http\Requests\Product\Movement\CreateProductMovementRequest;
use App\Http\Requests\Product\Movement\IndexByVariantProductMovementRequest;
use App\Http\Requests\Product\Movement\ShowProductMovementRequest;
use App\Http\Resources\Product\Movement\IndexByVariantProductMovementResource;
use App\Repositories\ProductMovementRepository;
use App\Services\ProductMovementService;

class ProductMovementController extends BaseController
{
    public function __construct(
        private ProductMovementService $service,
        private ProductMovementRepository $repository,
    ) {}

    public function indexByVariant(IndexByVariantProductMovementRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            $movements = $this->repository->getAllByEnterprise(filters: ['product_variant_id' => $request->route('productVariantID')]);

            return response()->json(['movements' => IndexByVariantProductMovementResource::collection($movements)], 200);
        }, 'Erro ao buscar movimentações do produto', $request);
    }

    public function show(ShowProductMovementRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            $movement = $this->repository->findById((int) $request->route('productMovementID'));

            return response()->json(['movement' => new IndexByVariantProductMovementResource($movement)], 200);
        }, 'Erro ao buscar movimentação', $request);
    }

    public function store(CreateProductMovementRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            $this->service->create($request);
            $message = match ($request->type) {
                'in' => 'Movimentação de entrada realizada',
                'out' => 'Movimentação de saída realizada',
                default => 'Movimentação realizada'
            };

            return response()->json(['message' => $message], 200);
        }, 'Erro ao movimentar produto', $request);
    }

    public function storeStockReentry(CreateProductMovementRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            $this->service->createWhenItsStockReentry($request);
            $message = match ($request->type) {
                'in' => 'Movimentação de entrada realizada',
                'out' => 'Movimentação de saída realizada',
                default => 'Movimentação realizada'
            };

            return response()->json(['message' => $message], 200);
        }, 'Erro ao movimentar produto devolvido', $request);
    }
}
