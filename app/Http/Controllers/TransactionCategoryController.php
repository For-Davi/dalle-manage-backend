<?php

namespace App\Http\Controllers;

use App\Http\Requests\Transaction\Category\CreateTransactionCategoryRequest;
use App\Http\Requests\Transaction\Category\DeleteTransactionCategoryRequest;
use App\Http\Requests\Transaction\Category\UpdateTransactionCategoryRequest;
use App\Repositories\TransactionCategoryRepository;
use App\Services\TransactionCategoryService;
use App\Utils\ErrorLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionCategoryController
{
    public function __construct(
        private TransactionCategoryService $service,
        private TransactionCategoryRepository $repository
    ) {}

    public function index(Request $request)
    {
        try {
            $categories = $this->repository->getAllByEnterprise();

            return response()->json(['categories' => $categories], 200);
        } catch (\Exception $e) {
            ErrorLogger::log('Erro ao buscar categorias:', $e, $request);

            return response()->json(['message' => 'Erro ao buscar categorias'], 500);
        }
    }

    public function store(CreateTransactionCategoryRequest $request)
    {
        try {
            DB::beginTransaction();
            $category = $this->service->create($request);

            if ($category) {
                DB::commit();

                $categories = $this->repository->getAllByEnterprise();

                return response()->json(['categories' => $categories, 'message' => 'Categoria cadastrada'], 201);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            ErrorLogger::log('Erro ao cadastrar categoria:', $e, $request);

            return response()->json(['message' => 'Erro ao cadastrar categoria'], 500);
        }
    }

    public function update(UpdateTransactionCategoryRequest $request)
    {
        try {
            DB::beginTransaction();
            $category = $this->service->update($request);

            if ($category) {
                DB::commit();

                $categories = $this->repository->getAllByEnterprise();

                return response()->json(['categories' => $categories, 'message' => 'Categoria atualizada'], 200);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            ErrorLogger::log('Erro ao atualizar categoria:', $e, $request);

            return response()->json(['message' => 'Erro ao atualizar categoria'], 500);
        }
    }

    public function destroy(DeleteTransactionCategoryRequest $request)
    {
        try {
            DB::beginTransaction();

            $category = $this->repository->delete($request->route('categoryID'));

            if ($category) {
                DB::commit();
                $categories = $this->repository->getAllByEnterprise();

                return response()->json(['categories' => $categories, 'message' => 'Categoria excluída'], 200);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            ErrorLogger::log('Erro ao excluir categoria:', $e, $request);

            return response()->json(['message' => 'Erro ao excluir categoria'], 500);
        }
    }
}
