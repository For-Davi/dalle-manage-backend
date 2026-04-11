<?php

namespace App\Http\Controllers;

use App\Http\Requests\Product\Movement\CreateProductMovementRequest;
use App\Services\ProductMovementService;

class ProductMovementController extends BaseController
{
    public function __construct(
        private ProductMovementService $service,
    ) {}

    public function store(CreateProductMovementRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            check_permission('product.movement');
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
            check_permission('product.movement');
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
