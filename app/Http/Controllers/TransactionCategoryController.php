<?php

namespace App\Http\Controllers;

use App\Http\Requests\Transaction\Category\CreateTransactionCategoryRequest;
use App\Http\Requests\Transaction\Category\DeleteTransactionCategoryRequest;
use App\Http\Requests\Transaction\Category\UpdateTransactionCategoryRequest;
use App\Repositories\TransactionCategoryRepository;
use App\Services\TransactionCategoryService;
use Illuminate\Http\Request;

class TransactionCategoryController extends BaseController
{
    public function __construct(
        private TransactionCategoryService $service,
        private TransactionCategoryRepository $repository
    ) {}

    public function index(Request $request)
    {
        return $this->safeExecute(function () {
            $categories = $this->repository->getAllByEnterprise();

            return response()->json(['categories' => $categories], 200);
        }, 'Erro ao buscar categorias', $request);
    }

    public function store(CreateTransactionCategoryRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            check_permission('transaction-category.create');
            $this->service->create($request);
            $categories = $this->repository->getAllByEnterprise();

            return response()->json(['categories' => $categories, 'message' => 'Categoria cadastrada'], 201);
        }, 'Erro ao cadastrar categoria', $request);
    }

    public function update(UpdateTransactionCategoryRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            check_permission('transaction-category.update');
            $this->service->update($request);
            $categories = $this->repository->getAllByEnterprise();

            return response()->json(['categories' => $categories, 'message' => 'Categoria atualizada'], 200);
        }, 'Erro ao atualizar categoria', $request);
    }

    public function destroy(DeleteTransactionCategoryRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            check_permission('transaction-category.delete');
            $this->repository->delete($request->route('categoryID'));
            $categories = $this->repository->getAllByEnterprise();

            return response()->json(['categories' => $categories, 'message' => 'Categoria excluída'], 200);
        }, 'Erro ao excluir categoria', $request);
    }
}
