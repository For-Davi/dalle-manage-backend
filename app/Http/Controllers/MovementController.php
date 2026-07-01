<?php

namespace App\Http\Controllers;

use App\DTO\Movement\FilterMovementDTO;
use App\Http\Requests\Movement\CreateMovementRequest;
use App\Http\Requests\Movement\DeleteMovementRequest;
use App\Http\Requests\Movement\ExportMovementRequest;
use App\Http\Requests\Movement\FilterMovementRequest;
use App\Http\Requests\Movement\ShowMovementRequest;
use App\Http\Requests\Movement\UpdateMovementRequest;
use App\Repositories\MovementRepository;
use App\Services\MovementService;
use Illuminate\Http\Request;

class MovementController extends BaseController
{
    public function __construct(
        private MovementService $service,
        private MovementRepository $repository
    ) {}

    public function index(Request $request)
    {
        return $this->safeExecute(function () {
            $movements = $this->repository->getAllByEnterpriseAndPeriod(true, ['category']);

            return response()->json(['movements' => $movements], 200);
        }, 'Erro ao buscar movimentações', $request);
    }

    public function indexPeriod(Request $request)
    {
        return $this->safeExecute(function () {
            $periods = $this->repository->getPeriods();

            return response()->json(['periods' => $periods], 200);
        }, 'Erro ao buscar períodos', $request);
    }

    public function show(ShowMovementRequest $request)
    {
        return $this->safeExecute(function () use ($request) {
            $movement = $this->repository->findById($request->route('movementID'), ['category']);

            return response()->json(['movement' => $movement], 200);
        }, 'Erro ao buscar movimentação', $request);
    }

    public function filter(FilterMovementRequest $request)
    {
        return $this->safeExecute(function () use ($request) {
            $movementFilterDTO = FilterMovementDTO::fromRequest($request);
            $movements = $this->repository->getAllWithFilter($movementFilterDTO->toArray(), ['category']);

            return response()->json(['movements' => $movements], 200);
        }, 'Erro ao filtrar movimentações', $request);
    }

    public function store(CreateMovementRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            check_plan('movements');
            check_permission('transaction.create');
            $this->service->create($request);
            $movements = $this->repository->getAllByEnterpriseAndPeriod(true, ['category']);

            return response()->json(['movements' => $movements, 'message' => 'Movimentação inserida'], 201);
        }, 'Erro ao inserir movimentação', $request);
    }

    public function export(ExportMovementRequest $request)
    {
        return $this->safeExecute(function () use ($request) {
            return $this->service->export($request);
        }, 'Erro ao exportar movimentações', $request);
    }

    public function update(UpdateMovementRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            check_permission('transaction.update');
            $this->service->update($request);
            $movements = $this->repository->getAllByEnterpriseAndPeriod(true, ['category']);

            return response()->json(['movements' => $movements, 'message' => 'Movimentação atualizada'], 200);
        }, 'Erro ao atualizar movimentação', $request);
    }

    public function destroy(DeleteMovementRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            check_permission('transaction.delete');
            $this->repository->delete($request->route('movementID'));
            $movements = $this->repository->getAllByEnterpriseAndPeriod(true, ['category']);

            return response()->json(['movements' => $movements, 'message' => 'Movimentação excluída'], 200);
        }, 'Erro ao excluir movimentação', $request);
    }
}
