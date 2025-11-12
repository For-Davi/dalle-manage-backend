<?php

namespace App\Http\Controllers;

use App\Http\Requests\Receipt\CreateReceiptRequest;
use App\Http\Requests\Receipt\DeleteReceiptRequest;
use App\Http\Requests\Receipt\ShowReceiptRequest;
use App\Http\Requests\Receipt\UpdateReceiptRequest;
use App\Repositories\ReceiptRepository;
use App\Services\ReceiptService;
use App\Utils\ErrorLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReceiptController
{
    public function __construct(
        private ReceiptService $service,
        private ReceiptRepository $repository
    ) {}

    public function index(Request $request)
    {
        try {
            $receipts = $this->repository->getAllByEnterprise();

            return response()->json(['receipts' => $receipts], 200);
        } catch (\Exception $e) {
            ErrorLogger::log('Erro ao buscar os recebimentos', $e, $request);

            return response()->json(['message' => 'Erro ao buscar os recebimentos'], 500);
        }
    }

    public function show(ShowReceiptRequest $request)
    {
        try {
            $receipt = $this->repository->findById($request->route('receiptID'));

            return response()->json(['receipt' => $receipt], 200);

        } catch (\Exception $e) {
            ErrorLogger::log('Erro ao buscar recebimento:', $e, $request);

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function store(CreateReceiptRequest $request)
    {
        try {
            DB::beginTransaction();
            $receipt = $this->service->create($request);
            if ($receipt) {
                DB::commit();

                $receipts = $this->repository->getAllByEnterprise();

                return response()->json(['receipts' => $receipts, 'message' => 'Recebimento cadastrado'], 201);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            ErrorLogger::log('Erro ao cadastrar tipo:', $e, $request);

            return response()->json(['message' => 'Erro ao cadastrar recebimento'], 500);
        }
    }

    public function update(UpdateReceiptRequest $request)
    {
        try {
            DB::beginTransaction();
            $receipt = $this->service->update($request);

            if ($receipt) {
                DB::commit();
                $receipts = $this->repository->getAllByEnterprise();

                return response()->json(['receipts' => $receipts, 'message' => 'Recebimento atualizado'], 200);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            ErrorLogger::log('Erro ao atualizar categoria:', $e, $request);

            return response()->json(['message' => 'Erro ao atualizar recebimento'], 500);
        }
    }

    public function destroy(DeleteReceiptRequest $request)
    {
        try {
            DB::beginTransaction();

            $receipt = $this->repository->delete($request->route('receiptID'));

            if ($receipt) {
                DB::commit();
                $receipts = $this->repository->getAllByEnterprise();

                return response()->json(['receipts' => $receipts, 'message' => 'Recebimento excluído'], 200);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            ErrorLogger::log('Erro ao excluir tipo de recebimento:', $e, $request);

            return response()->json(['message' => 'Erro ao excluir o recebimento'], 500);
        }
    }
}
