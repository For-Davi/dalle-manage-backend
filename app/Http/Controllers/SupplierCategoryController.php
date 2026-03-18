<?php

namespace App\Http\Controllers;

use App\Http\Requests\Supplier\Category\CreateSupplierCategoryRequest;
use App\Http\Requests\Supplier\Category\DeleteSupplierCategoryRequest;
use App\Http\Requests\Supplier\Category\ShowSupplierCategoryRequest;
use App\Http\Requests\Supplier\Category\UpdateSupplierCategoryRequest;
use App\Repositories\SupplierCategoryRepository;
use App\Services\SupplierCategoryService;
use Illuminate\Http\Request;

class SupplierCategoryController extends BaseController
{
    public function __construct(
        private SupplierCategoryService $service,
        private SupplierCategoryRepository $repository
    ) {}

    public function index(Request $request)
    {
        return $this->safeExecute(function () {
            $categories = $this->repository->getAllByEnterprise();

            return response()->json(['categories' => $categories], 200);
        }, 'Erro ao buscar categorias', $request);
    }

    public function show(ShowSupplierCategoryRequest $request)
    {
        return $this->safeExecute(function () use ($request) {
            $category = $this->repository->findById($request->route('categoryID'));

            return response()->json(['category' => $category], 200);
        }, 'Erro ao buscar categoria', $request);
    }

    public function store(CreateSupplierCategoryRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            $this->service->create($request);
            $categories = $this->repository->getAllByEnterprise();

            return response()->json(['categories' => $categories, 'message' => 'Categoria cadastrada'], 201);
        }, 'Erro ao cadastrar categoria', $request);
    }

    public function update(UpdateSupplierCategoryRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            $this->service->update($request);
            $categories = $this->repository->getAllByEnterprise();

            return response()->json(['categories' => $categories, 'message' => 'Categoria atualizada'], 200);
        }, 'Erro ao atualizar categoria', $request);
    }

    public function destroy(DeleteSupplierCategoryRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            $this->repository->delete($request->route('categoryID'));
            $categories = $this->repository->getAllByEnterprise();

            return response()->json(['categories' => $categories, 'message' => 'Categoria excluída'], 200);
        }, 'Erro ao excluir categoria', $request);
    }
}
