<?php

namespace App\Http\Controllers;

use App\Http\Requests\Return\CreateReturnRequest;
use App\Http\Requests\Return\IndexReturnRequest;
use App\Http\Resources\Return\ReturnResource;
use App\Http\Requests\Return\ShowReturnRequest;
use App\Http\Requests\Return\UpdateReturnRequest;
use App\Http\Resources\Return\ShowReturnResource;
use App\Http\Resources\Return\ShowLinkedReturnProductsResource;
use App\Repositories\ReturnRepository;
use App\Services\ReturnService;
use App\Utils\ErrorLogger;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ReturnController
{
    public function __construct(
        protected ReturnRepository $repository,
        protected ReturnService $service,
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

            return response()->json(['return' => new ShowLinkedReturnProductsResource($return)], 200);
        } catch (\Exception $e) {
            ErrorLogger::log('Erro ao buscar devoluções:', $e, $request);

            return response()->json(['message' => 'Erro ao buscar devoluções'], 500);
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
}
