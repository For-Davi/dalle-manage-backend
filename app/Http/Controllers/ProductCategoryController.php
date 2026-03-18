<?php

namespace App\Http\Controllers;

use App\Http\Requests\Product\Category\CreateProductCategoryRequest;
use App\Http\Requests\Product\Category\DeleteProductCategoryRequest;
use App\Http\Requests\Product\Category\UpdateProductCategoryRequest;
use App\Repositories\ProductCategoryRepository;
use App\Services\ProductCategoryService;
use Illuminate\Http\Request;

class ProductCategoryController extends BaseController
{
    public function __construct(
        private ProductCategoryService $service,
        private ProductCategoryRepository $repository
    ) {}

    public function index(Request $request)
    {
        return $this->safeExecute(function () {
            $categories = $this->repository->getAllByEnterprise();

            return response()->json(['categories' => $categories], 200);
        }, 'Erro ao buscar categorias', $request);
    }

    public function store(CreateProductCategoryRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            $this->service->create($request);
            $categories = $this->repository->getAllByEnterprise();

            return response()->json(['categories' => $categories, 'message' => 'Categoria cadastrada'], 201);
        }, 'Erro ao cadastrar categoria', $request);
    }

    public function update(UpdateProductCategoryRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            $this->service->update($request);
            $categories = $this->repository->getAllByEnterprise();

            return response()->json(['categories' => $categories, 'message' => 'Categoria atualizada'], 200);
        }, 'Erro ao atualizar categoria', $request);
    }

    public function destroy(DeleteProductCategoryRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            $this->repository->delete($request->route('categoryID'));
            $categories = $this->repository->getAllByEnterprise();

            return response()->json(['categories' => $categories, 'message' => 'Categoria excluída'], 200);
        }, 'Erro ao excluir categoria', $request);
    }
}
