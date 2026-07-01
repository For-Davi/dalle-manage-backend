<?php

namespace App\Http\Controllers;

use App\DTO\Receipt\FilterReceiptDTO;
use App\Http\Requests\Receipt\CreateReceiptRequest;
use App\Http\Requests\Receipt\DeleteReceiptRequest;
use App\Http\Requests\Receipt\FilterReceiptRequest;
use App\Http\Requests\Receipt\ShowReceiptRequest;
use App\Http\Requests\Receipt\UpdateReceiptRequest;
use App\Repositories\ReceiptRepository;
use App\Services\ReceiptService;
use Illuminate\Http\Request;

class ReceiptController extends BaseController
{
    public function __construct(
        private ReceiptService $service,
        private ReceiptRepository $repository
    ) {}

    public function index(Request $request)
    {
        return $this->safeExecute(function () {
            $receipts = $this->repository->getAllByEnterprise(['type']);

            return response()->json(['receipts' => $receipts], 200);
        }, 'Erro ao buscar os recebimentos', $request);
    }

    public function show(ShowReceiptRequest $request)
    {
        return $this->safeExecute(function () use ($request) {
            $receipt = $this->repository->findById($request->route('receiptID'));

            return response()->json(['receipt' => $receipt], 200);
        }, 'Erro ao buscar recebimento', $request);
    }

    public function filter(FilterReceiptRequest $request)
    {
        return $this->safeExecute(function () use ($request) {
            $receiptFilterDTO = FilterReceiptDTO::fromRequest($request);
            $receipts = $this->repository->getAllWithFilter($receiptFilterDTO);

            return response()->json(['receipts' => $receipts], 200);
        }, 'Erro ao filtrar os recebimentos', $request);
    }

    public function store(CreateReceiptRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            check_plan('receipts');
            check_permission('receipt.create');
            $this->service->create($request);
            $receipts = $this->repository->getAllByEnterprise(['type']);

            return response()->json(['receipts' => $receipts, 'message' => 'Recebimento cadastrado'], 201);
        }, 'Erro ao cadastrar recebimento', $request);
    }

    public function update(UpdateReceiptRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            check_permission('receipt.update');
            $this->service->update($request);
            $receipts = $this->repository->getAllByEnterprise(['type']);

            return response()->json(['receipts' => $receipts, 'message' => 'Recebimento atualizado'], 200);
        }, 'Erro ao atualizar recebimento', $request);
    }

    public function destroy(DeleteReceiptRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            check_permission('receipt.delete');
            $this->repository->delete($request->route('receiptID'));
            $receipts = $this->repository->getAllByEnterprise(['type']);

            return response()->json(['receipts' => $receipts, 'message' => 'Recebimento excluído'], 200);
        }, 'Erro ao excluir recebimento', $request);
    }
}
