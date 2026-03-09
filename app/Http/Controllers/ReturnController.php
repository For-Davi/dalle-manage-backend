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
use App\Utils\ErrorLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReturnController
{
    public function __construct(
        protected ReturnRepository $repository,
        protected ReturnService $service,
        protected StockReentryReturnItemRepository $stockReentryReturnItemRepository,
    ) {}

    public function index(IndexReturnRequest $request)
    {
        try {
            $returns = $this->repository->getAllBySale($request->route('saleID'));

            $returns->load(['returnExchangeItems']);

            return response()->json(['returns' => ReturnResource::collection($returns)], 200);
        } catch (\Exception $e) {
            ErrorLogger::log('Erro ao buscar devoluções:', $e, $request);

            return response()->json(['message' => 'Erro ao buscar devoluções'], 500);
        }
    }

    public function show(ShowReturnRequest $request)
    {
        try {
            $return = $this->repository->findById($request->route('returnID'));

            $return->load(['items', 'returnExchangeItems']);

            return response()->json(['return' => new ShowReturnResource($return)], 200);
        } catch (\Exception $e) {
            ErrorLogger::log('Erro ao buscar devoluções:', $e, $request);

            return response()->json(['message' => 'Erro ao buscar devoluções'], 500);
        }
    }

    public function showLinked(ShowReturnRequest $request)
    {
        try {
            $return = $this->repository->findById($request->route('returnID'));

            $return->load(['returnExchangeItems']);

            return response()->json(['products' => new ShowLinkedReturnProductsResource($return)], 200);
        } catch (\Exception $e) {
            ErrorLogger::log('Erro ao buscar devoluções:', $e, $request);

            return response()->json(['message' => 'Erro ao buscar devoluções'], 500);
        }
    }

    public function showStockReentry(Request $request)
    {
        try {
            $stockReentryItem = $this->stockReentryReturnItemRepository->getAllByEnterprise();

            return response()->json(['products' => $stockReentryItem], 200);
        } catch (\Exception $e) {
            ErrorLogger::log('Erro ao buscar produtos de reentrada de estoque:', $e, $request);

            return response()->json(['message' => 'Erro ao buscar produtos de reentrada de estoque'], 500);
        }
    }

    public function store(CreateReturnRequest $request)
    {
        try {
            DB::beginTransaction();

            $result = $this->service->create($request);

            if ($result) {
                DB::commit();

                $returns = $this->repository->getAllBySale($request['saleID']);

                return response()->json(['returns' => ReturnResource::collection($returns), 'message' => 'Registro de devolução criado'], 201);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            ErrorLogger::log('Erro ao criar devolução:', $e, $request);

            return response()->json(['message' => 'Erro ao criar devolução'], 500);
        }
    }

    public function update(UpdateReturnRequest $request)
    {
        try {
            DB::beginTransaction();

            $result = $this->service->update($request);

            if ($result) {
                DB::commit();

                $returns = $this->repository->getAllBySale($request['saleID']);

                return response()->json(['returns' => ReturnResource::collection($returns), 'message' => 'Devolução atualizada'], 200);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            ErrorLogger::log('Erro ao criar devolução:', $e, $request);

            return response()->json(['message' => 'Erro ao atualizar devolução'], 500);
        }
    }

    public function destroy(DeleteReturnRequest $request)
    {
        try {
            DB::beginTransaction();

            $return = $this->repository->deleteReturn($request->route('returnID'));

            if ($return) {
                DB::commit();
                $returns = $this->repository->getAllBySale($request->route('saleID'));

                return response()->json(['returns' => ReturnResource::collection($returns), 'message' => 'Devolução excluída'], 200);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            ErrorLogger::log('Erro ao excluir devolução:', $e, $request);

            return response()->json(['message' => 'Erro ao excluir devolução'], 500);
        }
    }
}
