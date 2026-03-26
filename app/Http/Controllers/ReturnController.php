<?php

namespace App\Http\Controllers;

use App\Http\Requests\Return\CreateReturnRequest;
use App\Http\Requests\Return\DeleteReturnRequest;
use App\Http\Requests\Return\IndexReturnRequest;
use App\Http\Requests\Return\ShowReturnRequest;
use App\Http\Requests\Return\UpdateReturnRequest;
use App\Http\Resources\Return\ReturnResource;
use App\Http\Resources\Return\ShowLinkedReturnProductsResource;
use App\Http\Resources\Return\ShowReturnResource;
use App\Repositories\ReturnRepository;
use App\Repositories\StockReentryReturnItemRepository;
use App\Services\ReturnService;
use Illuminate\Http\Request;

class ReturnController extends BaseController
{
    public function __construct(
        protected ReturnRepository $repository,
        protected ReturnService $service,
        protected StockReentryReturnItemRepository $stockReentryReturnItemRepository,
    ) {}

    public function index(IndexReturnRequest $request)
    {
        return $this->safeExecute(function () use ($request) {
            $returns = $this->repository->getAllBySale($request->route('saleID'));
            $returns->load(['returnExchangeItems']);

            return response()->json(['returns' => ReturnResource::collection($returns)], 200);
        }, 'Erro ao buscar devoluções', $request);
    }

    public function show(ShowReturnRequest $request)
    {
        return $this->safeExecute(function () use ($request) {
            $return = $this->repository->findById($request->route('returnID'));
            $return->load(['items', 'returnExchangeItems']);

            return response()->json(['return' => new ShowReturnResource($return)], 200);
        }, 'Erro ao buscar devolução', $request);
    }

    public function showLinked(ShowReturnRequest $request)
    {
        return $this->safeExecute(function () use ($request) {
            $return = $this->repository->findById($request->route('returnID'));
            $return->load(['returnExchangeItems']);

            return response()->json(['products' => new ShowLinkedReturnProductsResource($return)], 200);
        }, 'Erro ao buscar devoluções vinculadas', $request);
    }

    public function showStockReentry(Request $request)
    {
        return $this->safeExecute(function () {
            $stockReentryItem = $this->stockReentryReturnItemRepository->getAllByEnterprise();

            return response()->json(['products' => $stockReentryItem], 200);
        }, 'Erro ao buscar produtos de reentrada de estoque', $request);
    }

    public function store(CreateReturnRequest $request)
    {
        dd('dados', $request);
        return $this->safeTransaction(function () use ($request) {
            $this->service->create($request);
            $returns = $this->repository->getAllBySale($request['saleID']);

            return response()->json(['returns' => ReturnResource::collection($returns), 'message' => 'Registro de devolução criado'], 201);
        }, 'Erro ao criar devolução', $request);
    }

    public function update(UpdateReturnRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            $this->service->update($request);
            $returns = $this->repository->getAllBySale($request['saleID']);

            return response()->json(['returns' => ReturnResource::collection($returns), 'message' => 'Devolução atualizada'], 200);
        }, 'Erro ao atualizar devolução', $request);
    }

    public function destroy(DeleteReturnRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            $this->repository->deleteReturn($request->route('returnID'));
            $returns = $this->repository->getAllBySale($request->route('saleID'));

            return response()->json(['returns' => ReturnResource::collection($returns), 'message' => 'Devolução excluída'], 200);
        }, 'Erro ao excluir devolução', $request);
    }
}
