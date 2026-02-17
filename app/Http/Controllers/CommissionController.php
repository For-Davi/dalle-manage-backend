<?php

namespace App\Http\Controllers;

use App\Http\Requests\Commission\IndexCommissionRequest;
use App\Http\Resources\Commission\CommissionResource;
use App\Repositories\CommissionRepository;
use App\Utils\ErrorLogger;

class CommissionController
{
    public function __construct(
        private CommissionRepository $repository
    ) {}

    public function index(IndexCommissionRequest $request)
    {
        try {
            $commissions = $this->repository->getAllBySale($request->route('saleID'));

            return response()->json(['commissions' => CommissionResource::collection($commissions)], 200);
        } catch (\Exception $e) {
            ErrorLogger::log('Erro ao buscar comissões:', $e, $request);

            return response()->json(['message' => 'Erro ao buscar comissões'], 500);
        }
    }
}
