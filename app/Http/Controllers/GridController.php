<?php

namespace App\Http\Controllers;

use App\Http\Requests\Grid\Group\CreateGridGroupRequest;
use App\Http\Requests\Grid\Group\DeleteGridGroupRequest;
use App\Http\Requests\Grid\Group\UpdateGridGroupRequest;
use App\Http\Requests\Grid\Item\CreateGridItemRequest;
use App\Http\Requests\Grid\Item\DeleteGridItemRequest;
use App\Http\Requests\Grid\Item\ShowAllGridItemRequest;
use App\Http\Requests\Grid\Item\ShowGridItemRequest;
use App\Http\Requests\Grid\Item\UpdateGridItemRequest;
use App\Repositories\GridGroupRepository;
use App\Repositories\GridItemRepository;
use App\Services\GridGroupService;
use App\Services\GridItemService;
use App\Utils\ErrorLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GridController
{
    public function __construct(
        private GridGroupService $gridGroupService,
        private GridItemService $gridItemService,
        private GridGroupRepository $gridGroupRepository,
        private GridItemRepository $gridItemRepository
    ) {}

    public function index(Request $request)
    {
        try {
            $gridGroups = $this->gridGroupRepository->getAllByEnterprise('items');

            return response()->json(['grids' => $gridGroups], 200);
        } catch (\Exception $e) {
            ErrorLogger::log('Erro ao buscar grupos de tamanhos:', $e, $request);

            return response()->json(['message' => 'Erro ao buscar grupos de tamanhos'], 500);
        }
    }

    public function indexItens(ShowAllGridItemRequest $request)
    {
        try {
            $gridItens = $this->gridItemRepository->getAllByGroup($request->route('gridID'));

            return response()->json(['gridItens' => $gridItens], 200);
        } catch (\Exception $e) {
            ErrorLogger::log('Erro ao buscar itens da grade:', $e, $request);

            return response()->json(['message' => 'Erro ao buscar itens da grade'], 500);
        }
    }

    public function showItem(ShowGridItemRequest $request)
    {
        try {
            $gridItem = $this->gridItemRepository->findById($request->route('itemID'));

            return response()->json(['gridItem' => $gridItem], 200);
        } catch (\Exception $e) {
            ErrorLogger::log('Erro ao buscar item da grade:', $e, $request);

            return response()->json(['message' => 'Erro ao buscar item da grade'], 500);
        }
    }

    public function store(CreateGridGroupRequest $request)
    {
        try {
            DB::beginTransaction();
            $grid = $this->gridGroupService->create($request);

            if ($grid) {
                DB::commit();

                $gridGroups = $this->gridGroupRepository->getAllByEnterprise('items');

                return response()->json(['grids' => $gridGroups, 'message' => 'Grade de tamanhos cadastrada'], 201);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            ErrorLogger::log('Erro ao cadastrar grade:', $e, $request);

            return response()->json(['message' => 'Erro ao cadastrar grade'], 500);
        }
    }

    public function storeItem(CreateGridItemRequest $request)
    {
        try {
            DB::beginTransaction();
            $item = $this->gridItemService->create($request);

            if ($item) {
                DB::commit();

                $gridGroups = $this->gridGroupRepository->getAllByEnterprise('items');

                return response()->json(['gridGroups' => $gridGroups, 'message' => 'Grade de tamanhos cadastrada'], 201);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            ErrorLogger::log('Erro ao cadastrar grade:', $e, $request);

            return response()->json(['message' => 'Erro ao cadastrar grade'], 500);
        }
    }

    public function update(UpdateGridGroupRequest $request)
    {
        try {
            DB::beginTransaction();
            $grid = $this->gridGroupService->update($request);

            if ($grid) {
                DB::commit();
                $gridGroups = $this->gridGroupRepository->getAllByEnterprise('items');

                return response()->json(['grids' => $gridGroups, 'message' => 'Grade de tamanhos atualizada'], 200);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            ErrorLogger::log('Erro ao atualizar grade:', $e, $request);

            return response()->json(['message' => 'Erro ao atualizar grade'], 500);
        }
    }

    public function updateItem(UpdateGridItemRequest $request)
    {
        try {
            DB::beginTransaction();
            $item = $this->gridItemService->update($request);

            if ($item) {
                DB::commit();
                $gridGroups = $this->gridGroupRepository->getAllByEnterprise('items');

                return response()->json(['gridGroups' => $gridGroups, 'message' => 'Grade de tamanhos atualizada'], 200);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            ErrorLogger::log('Erro ao atualizar grade:', $e, $request);

            return response()->json(['message' => 'Erro ao atualizar grade'], 500);
        }
    }

    public function destroy(DeleteGridGroupRequest $request)
    {
        try {
            DB::beginTransaction();

            $grid = $this->gridGroupRepository->delete($request->route('gridID'));

            if ($grid) {
                DB::commit();
                $gridGroups = $this->gridGroupRepository->getAllByEnterprise('items');

                return response()->json(['grids' => $gridGroups, 'message' => 'Grade de tamanhos excluída'], 200);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            ErrorLogger::log('Erro ao excluir grade:', $e, $request);

            return response()->json(['message' => 'Erro ao excluir grade'], 500);
        }
    }

    public function destroyItem(DeleteGridItemRequest $request)
    {
        try {
            DB::beginTransaction();

            $grid = $this->gridItemRepository->delete($request->route('itemID'));

            if ($grid) {
                DB::commit();
                $gridGroups = $this->gridGroupRepository->getAllByEnterprise('items');

                return response()->json(['gridGroups' => $gridGroups, 'message' => 'Item da grade excluído'], 200);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            ErrorLogger::log('Erro ao excluir item da grade:', $e, $request);

            return response()->json(['message' => 'Erro ao excluir item da grade'], 500);
        }
    }
}
