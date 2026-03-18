<?php

namespace App\Http\Controllers;

use App\DTO\Supplier\FilterSupplierDTO;
use App\Http\Requests\Supplier\CreateSupplierRequest;
use App\Http\Requests\Supplier\DeleteSupplierRequest;
use App\Http\Requests\Supplier\FilterSupplierRequest;
use App\Http\Requests\Supplier\ShowSupplierRequest;
use App\Http\Requests\Supplier\UpdateSupplierRequest;
use App\Repositories\SupplierRepository;
use App\Services\SupplierService;
use Illuminate\Http\Request;

class SupplierController extends BaseController
{
    public function __construct(
        protected SupplierService $service,
        protected SupplierRepository $repository
    ) {}

    public function index(Request $request)
    {
        return $this->safeExecute(function () {
            $suppliers = $this->repository->getAllByEnterprise();

            return response()->json(['suppliers' => $suppliers], 200);
        }, 'Erro ao buscar fornecedores', $request);
    }

    public function list(Request $request)
    {
        return $this->safeExecute(function () {
            $suppliers = $this->repository->getAllByEnterprise([], ['id', 'name']);

            return response()->json(['suppliers' => $suppliers], 200);
        }, 'Erro ao buscar fornecedores', $request);
    }

    public function show(ShowSupplierRequest $request)
    {
        return $this->safeExecute(function () use ($request) {
            $supplier = $this->repository->findById($request->route('supplierID'));

            return response()->json(['supplier' => $supplier], 200);
        }, 'Erro ao buscar fornecedor', $request);
    }

    public function filter(FilterSupplierRequest $request)
    {
        return $this->safeExecute(function () use ($request) {
            $supplierFilterDTO = FilterSupplierDTO::fromRequest($request);
            $suppliers = $this->repository->getAllWithFilter($supplierFilterDTO);

            return response()->json(['suppliers' => $suppliers], 200);
        }, 'Erro ao filtrar fornecedores', $request);
    }

    public function store(CreateSupplierRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            $this->service->create($request);
            $suppliers = $this->repository->getAllByEnterprise();

            return response()->json(['suppliers' => $suppliers, 'message' => 'Fornecedor cadastrado'], 201);
        }, 'Erro ao cadastrar fornecedor', $request);
    }

    public function update(UpdateSupplierRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            $this->service->update($request);
            $suppliers = $this->repository->getAllByEnterprise();

            return response()->json(['suppliers' => $suppliers, 'message' => 'Fornecedor atualizado'], 200);
        }, 'Erro ao atualizar fornecedor', $request);
    }

    public function destroy(DeleteSupplierRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            $this->repository->delete($request->route('supplierID'));
            $suppliers = $this->repository->getAllByEnterprise();

            return response()->json(['suppliers' => $suppliers, 'message' => 'Fornecedor excluído'], 200);
        }, 'Erro ao excluir fornecedor', $request);
    }
}
