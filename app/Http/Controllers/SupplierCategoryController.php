<?php

namespace App\Http\Controllers;

use App\Http\Requests\Supplier\Category\CreateSupplierCategoryRequest;
use App\Http\Requests\Supplier\Category\DeleteSupplierCategoryRequest;
use App\Http\Requests\Supplier\Category\ShowSupplierCategoryRequest;
use App\Http\Requests\Supplier\Category\UpdateSupplierCategoryRequest;
use App\Repositories\SupplierCategoryRepository;
use App\Services\SupplierCategoryService;
use App\Utils\ErrorLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierCategoryController
{
    public function __construct(
        private SupplierCategoryService $service,
        private SupplierCategoryRepository $repository
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

    public function show(ShowSupplierCategoryRequest $request)
    {
        try {
            $category = $this->repository->findById($request->route('categoryID'));

            return response()->json(['category' => $category], 200);

        } catch (\Exception $e) {
            ErrorLogger::log('Erro ao buscar categoria:', $e, $request);

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function store(CreateSupplierCategoryRequest $request)
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

    public function update(UpdateSupplierCategoryRequest $request)
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

    public function destroy(DeleteSupplierCategoryRequest $request)
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
