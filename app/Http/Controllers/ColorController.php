<?php

namespace App\Http\Controllers;

use App\Http\Requests\Product\Color\CreateProductColorRequest;
use App\Http\Requests\Product\Color\DeleteProductColorRequest;
use App\Http\Requests\Product\Color\UpdateProductColorRequest;
use App\Repositories\ProductColorRepository;
use App\Services\ProductColorService;
use App\Utils\ErrorLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ColorController
{
    public function __construct(
        private ProductColorService $service,
        private ProductColorRepository $repository
    ) {}

    public function index(Request $request)
    {
        try {
            $colors = $this->repository->getAllByEnterprise();

            return response()->json(['colors' => $colors], 200);
        } catch (\Exception $e) {
            ErrorLogger::log('Erro ao buscar cores:', $e, $request);

            return response()->json(['message' => 'Erro ao buscar cores'], 500);
        }
    }

    public function store(CreateProductColorRequest $request)
    {
        try {
            DB::beginTransaction();
            $color = $this->service->create($request);

            if ($color) {
                DB::commit();
                $colors = $this->repository->getAllByEnterprise();

                return response()->json(['colors' => $colors, 'message' => 'Cor cadastrada'], 201);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            ErrorLogger::log('Erro ao cadastrar cor:', $e, $request);

            return response()->json(['message' => 'Erro ao cadastrar cor'], 500);
        }
    }

    public function update(UpdateProductColorRequest $request)
    {
        try {
            DB::beginTransaction();
            $color = $this->service->update($request);

            if ($color) {
                DB::commit();

                $colors = $this->repository->getAllByEnterprise();

                return response()->json(['colors' => $colors, 'message' => 'Cor atualizada'], 200);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            ErrorLogger::log('Erro ao atualizar cor:', $e, $request);

            return response()->json(['message' => 'Erro ao atualizar cor'], 500);
        }
    }

    public function destroy(DeleteProductColorRequest $request)
    {
        try {
            DB::beginTransaction();

            $color = $this->repository->delete($request->route('colorID'));

            if ($color) {
                DB::commit();
                $colors = $this->repository->getAllByEnterprise();

                return response()->json(['colors' => $colors, 'message' => 'Cor excluída'], 200);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            ErrorLogger::log('Erro ao excluir cor:', $e, $request);

            return response()->json(['message' => 'Erro ao excluir cor'], 500);
        }
    }
}
