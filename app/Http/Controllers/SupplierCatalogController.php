<?php

namespace App\Http\Controllers;

use App\Http\Requests\Supplier\Catalog\CreateCatalogSupplierRequest;
use App\Http\Requests\Supplier\Catalog\DeleteCatalogSupplierRequest;
use App\Http\Requests\Supplier\Catalog\GetByVariantCatalogSupplierRequest;
use App\Http\Requests\Supplier\Catalog\UpdateCatalogSupplierRequest;
use App\Http\Resources\Product\ProductsLinkedResource;
use App\Repositories\SupplierCatalogRepository;
use App\Services\SupplierCatalogService;
use Illuminate\Http\Request;

class SupplierCatalogController extends BaseController
{
    public function __construct(
        private SupplierCatalogService $service,
        private SupplierCatalogRepository $repository
    ) {}

    public function index(Request $request)
    {
        return $this->safeExecute(function () use ($request) {
            $catalog = $this->repository->getBySupplier($request->route('supplierID'), ['variant.product', 'variant.color', 'supplier']);

            return response()->json(['catalog' => ProductsLinkedResource::collection($catalog)], 200);
        }, 'Erro ao buscar catálogos', $request);
    }

    public function getByVariant(GetByVariantCatalogSupplierRequest $request)
    {
        return $this->safeExecute(function () use ($request) {
            $catalog = $this->repository->getByVariant($request->variantID, ['variant.product', 'variant.color', 'supplier']);

            return response()->json(['catalog' => ProductsLinkedResource::collection($catalog)], 200);
        }, 'Erro ao buscar fornecedores por produto', $request);
    }

    public function store(CreateCatalogSupplierRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            $this->service->create($request);
            $catalog = $this->repository->getBySupplier($request->supplierID, ['variant.product', 'variant.color', 'supplier']);

            return response()->json(['catalog' => ProductsLinkedResource::collection($catalog), 'message' => 'Item de catálogo cadastrado'], 201);
        }, 'Erro ao cadastrar item de catálogo', $request);
    }

    public function update(UpdateCatalogSupplierRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            $this->service->update($request);
            $catalog = $this->repository->getBySupplier($request->supplierID, ['variant.product', 'variant.color', 'supplier']);

            return response()->json(['catalog' => ProductsLinkedResource::collection($catalog), 'message' => 'Item de catálogo atualizado'], 200);
        }, 'Erro ao atualizar item do catálogo', $request);
    }

    public function destroy(DeleteCatalogSupplierRequest $request, $supplierID, $productVariantID)
    {
        return $this->safeTransaction(function () use ($request, $supplierID, $productVariantID) {
            $this->repository->delete($supplierID, $productVariantID);
            $catalog = $this->repository->getBySupplier($request->supplierID, ['variant.product', 'variant.color', 'supplier']);

            return response()->json(['catalog' => ProductsLinkedResource::collection($catalog), 'message' => 'Item de catálogo excluído'], 200);
        }, 'Erro ao excluir item de catálogo', $request);
    }
}
