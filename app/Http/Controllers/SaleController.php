<?php

namespace App\Http\Controllers;

use App\Http\Requests\Sale\CreateSaleRequest;
use App\Utils\ErrorLogger;
use Illuminate\Support\Facades\DB;

class SaleController
{
    public function __construct(
    ) {}

    public function store(CreateSaleRequest $request)
    {

        dd('dados da venda', $request);
        // try {
        //     DB::beginTransaction();
        //     $client = $this->service->create($request);
        //     if ($client) {
        //         DB::commit();

        //         $clients = $this->repository->getAllByEnterprise($request->get('enterprise_id'));

        //         return response()->json(['clients' => $clients, 'message' => 'Cliente cadastrado'], 201);
        //     }
        // } catch (\Exception $e) {
        //     DB::rollBack();

        //     ErrorLogger::log('Erro ao cadastrar cliente:', $e, $request);

        //     return response()->json(['message' => 'Erro ao cadastrar cliente'], 500);
        // }
    }
}
