<?php

namespace App\Http\Controllers;

use App\Http\Requests\Sale\CreateSaleRequest;
use App\Services\SaleService;
use App\Utils\ErrorLogger;
use Illuminate\Support\Facades\DB;

class SaleController
{
    public function __construct(
      private  SaleService $service
    ) {}

    public function store(CreateSaleRequest $request)
    {
        try {
            DB::beginTransaction();
            $sale = $this->service->create($request);
            if ($sale) {
                DB::commit();

                return response()->json(['message' => 'Venda feita com sucesso'], 201);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            ErrorLogger::log('Erro ao fazer a venda:', $e, $request);

            return response()->json(['message' => 'Erro ao fazer a venda'], 500);
        }
    }
}
