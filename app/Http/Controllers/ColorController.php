<?php

namespace App\Http\Controllers;

use App\Http\Requests\Product\Color\CreateProductColorRequest;
use App\Http\Requests\Product\Color\DeleteProductColorRequest;
use App\Http\Requests\Product\Color\UpdateProductColorRequest;
use App\Repositories\ProductColorRepository;
use App\Services\ProductColorService;
use Illuminate\Http\Request;

class ColorController extends BaseController
{
    public function __construct(
        private ProductColorService $service,
        private ProductColorRepository $repository
    ) {}

    public function index(Request $request)
    {
        return $this->safeExecute(function () {
            $colors = $this->repository->getAllByEnterprise();

            return response()->json(['colors' => $colors], 200);
        }, 'Erro ao buscar cores', $request);
    }

    public function store(CreateProductColorRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            check_plan('product_colors');
            check_permission('product-color.create');
            $this->service->create($request);
            $colors = $this->repository->getAllByEnterprise();

            return response()->json(['colors' => $colors, 'message' => 'Cor cadastrada'], 201);
        }, 'Erro ao cadastrar cor', $request);
    }

    public function update(UpdateProductColorRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            check_permission('product-color.update');
            $this->service->update($request);
            $colors = $this->repository->getAllByEnterprise();

            return response()->json(['colors' => $colors, 'message' => 'Cor atualizada'], 200);
        }, 'Erro ao atualizar cor', $request);
    }

    public function destroy(DeleteProductColorRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            check_permission('product-color.delete');
            $this->repository->delete($request->route('colorID'));
            $colors = $this->repository->getAllByEnterprise();

            return response()->json(['colors' => $colors, 'message' => 'Cor excluída'], 200);
        }, 'Erro ao excluir cor', $request);
    }
}
