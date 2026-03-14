<?php

namespace App\Http\Controllers;

use App\Http\Requests\Product\Movement\CreateProductMovementRequest;
use App\Services\ProductMovementService;
use App\Utils\ErrorLogger;
use Illuminate\Support\Facades\DB;

class ProductMovementController
{
    public function __construct(
        private ProductMovementService $service,
    ) {}

    public function store(CreateProductMovementRequest $request)
    {
        try {
            DB::beginTransaction();
            $movement = $this->service->create($request);

            if ($movement) {
                DB::commit();

                $message = match ($request->type) {
                    'in' => 'Movimentação de entrada realizada',
                    'out' => 'Movimentação de saída realizada',
                    default => 'Movimentação realizada'
                };

                return response()->json(['message' => $message], 200);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            ErrorLogger::log('Erro ao movimentar produto:', $e, $request);

            return response()->json(['message' => 'Erro ao movimentar produto'], 500);
        }
    }

    public function storeStockReentry(CreateProductMovementRequest $request)
    {
        try {
            DB::beginTransaction();
            $movement = $this->service->createWhenItsStockReentry($request);

            if ($movement) {
                DB::commit();

                $message = match ($request->type) {
                    'in' => 'Movimentação de entrada realizada',
                    'out' => 'Movimentação de saída realizada',
                    default => 'Movimentação realizada'
                };

                return response()->json(['message' => $message], 200);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            ErrorLogger::log('Erro ao movimentar produto devolvido:', $e, $request);

            return response()->json(['message' => 'Erro ao movimentar produto devolvido'], 500);
        }
    }
}
