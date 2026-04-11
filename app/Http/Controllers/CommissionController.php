<?php

namespace App\Http\Controllers;

use App\DTO\Commission\FilterCommissionDTO;
use App\Http\Requests\Commission\ExportCommissionRequest;
use App\Http\Requests\Commission\FilterCommissionRequest;
use App\Http\Requests\Commission\IndexCommissionRequest;
use App\Http\Resources\Commission\CommissionResource;
use App\Repositories\CommissionRepository;
use App\Services\CommissionService;

class CommissionController extends BaseController
{
    public function __construct(
        private CommissionRepository $repository,
        private CommissionService $service,
    ) {}

    public function index(FilterCommissionRequest $request)
    {
        return $this->safeExecute(function () use ($request) {
            check_permission('commission.view');
            $commissionFilterDTO = FilterCommissionDTO::fromRequest($request);
            $commissions = $this->repository->getAllWithFilter($commissionFilterDTO->toArray());

            return response()->json(['commissions' => $commissions], 200);
        }, 'Erro ao buscar comissões', $request);
    }

    public function export(ExportCommissionRequest $request)
    {
        return $this->safeExecute(function () use ($request) {
            return $this->service->export($request);
        }, 'Erro ao exportar detalhes de comissão', $request);
    }

    public function showBySale(IndexCommissionRequest $request)
    {
        return $this->safeExecute(function () use ($request) {
            $commissions = $this->repository->getAllBySale($request->route('saleID'));

            return response()->json(['commissions' => CommissionResource::collection($commissions)], 200);
        }, 'Erro ao buscar comissões', $request);
    }
}
