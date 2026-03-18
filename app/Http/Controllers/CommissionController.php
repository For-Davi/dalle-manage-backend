<?php

namespace App\Http\Controllers;

use App\Http\Requests\Commission\IndexCommissionRequest;
use App\Http\Resources\Commission\CommissionResource;
use App\Repositories\CommissionRepository;

class CommissionController extends BaseController
{
    public function __construct(
        private CommissionRepository $repository
    ) {}

    public function index(IndexCommissionRequest $request)
    {
        return $this->safeExecute(function () use ($request) {
            $commissions = $this->repository->getAllBySale($request->route('saleID'));

            return response()->json(['commissions' => CommissionResource::collection($commissions)], 200);
        }, 'Erro ao buscar comissões', $request);
    }
}
