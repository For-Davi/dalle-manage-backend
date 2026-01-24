<?php

namespace App\Http\Controllers;

use App\Http\Requests\Return\CreateReturnRequest;
use App\Repositories\ReturnRepository;
use App\Services\ReturnService;
use App\Utils\ErrorLogger;
use Illuminate\Support\Facades\DB;

class ReturnController
{
    public function __construct(
        protected ReturnRepository $repository,
        protected ReturnService $service,
    ) {}

    public function store(CreateReturnRequest $request)
    {
        try {
            DB::beginTransaction();

            $result = $this->service->create($request);

            if ($result) {
                DB::commit();

                $returns = $this->repository->getAllBySale($request['saleID']);

                return response()->json(['returns' => $returns, 'message' => 'Registro de devolução criado'], 200);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            ErrorLogger::log('Erro ao criar devolução:', $e, $request);

            return response()->json(['message' => 'Erro ao criar devolução'], 500);
        }
    }
}
