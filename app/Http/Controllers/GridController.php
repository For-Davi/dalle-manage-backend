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
use Illuminate\Http\Request;

class GridController extends BaseController
{
    public function __construct(
        private GridGroupService $gridGroupService,
        private GridItemService $gridItemService,
        private GridGroupRepository $gridGroupRepository,
        private GridItemRepository $gridItemRepository
    ) {}

    public function index(Request $request)
    {
        return $this->safeExecute(function () {
            $gridGroups = $this->gridGroupRepository->getAllByEnterprise(['items']);

            return response()->json(['grids' => $gridGroups], 200);
        }, 'Erro ao buscar grupos de tamanhos', $request);
    }

    public function indexItens(ShowAllGridItemRequest $request)
    {
        return $this->safeExecute(function () use ($request) {
            $gridItens = $this->gridItemRepository->getAllByGroup($request->route('gridID'));

            return response()->json(['gridItens' => $gridItens], 200);
        }, 'Erro ao buscar itens da grade', $request);
    }

    public function showItem(ShowGridItemRequest $request)
    {
        return $this->safeExecute(function () use ($request) {
            $gridItem = $this->gridItemRepository->findById($request->route('itemID'));

            return response()->json(['gridItem' => $gridItem], 200);
        }, 'Erro ao buscar item da grade', $request);
    }

    public function store(CreateGridGroupRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            check_permission('grid.create');
            $this->gridGroupService->create($request);
            $gridGroups = $this->gridGroupRepository->getAllByEnterprise(['items']);

            return response()->json(['grids' => $gridGroups, 'message' => 'Grade de tamanhos cadastrada'], 201);
        }, 'Erro ao cadastrar grade', $request);
    }

    public function storeItem(CreateGridItemRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            check_permission('grid.create');
            $this->gridItemService->create($request);
            $gridGroups = $this->gridGroupRepository->getAllByEnterprise(['items']);

            return response()->json(['gridGroups' => $gridGroups, 'message' => 'Grade de tamanhos cadastrada'], 201);
        }, 'Erro ao cadastrar grade', $request);
    }

    public function update(UpdateGridGroupRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            check_permission('grid.update');
            $this->gridGroupService->update($request);
            $gridGroups = $this->gridGroupRepository->getAllByEnterprise(['items']);

            return response()->json(['grids' => $gridGroups, 'message' => 'Grade de tamanhos atualizada'], 200);
        }, 'Erro ao atualizar grade', $request);
    }

    public function updateItem(UpdateGridItemRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            check_permission('grid.update');
            $this->gridItemService->update($request);
            $gridGroups = $this->gridGroupRepository->getAllByEnterprise(['items']);

            return response()->json(['gridGroups' => $gridGroups, 'message' => 'Grade de tamanhos atualizada'], 200);
        }, 'Erro ao atualizar grade', $request);
    }

    public function destroy(DeleteGridGroupRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            check_permission('grid.delete');
            $this->gridGroupRepository->delete($request->route('gridID'));
            $gridGroups = $this->gridGroupRepository->getAllByEnterprise(['items']);

            return response()->json(['grids' => $gridGroups, 'message' => 'Grade de tamanhos excluída'], 200);
        }, 'Erro ao excluir grade', $request);
    }

    public function destroyItem(DeleteGridItemRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            check_permission('grid.delete');
            $this->gridItemRepository->delete($request->route('itemID'));
            $gridGroups = $this->gridGroupRepository->getAllByEnterprise(['items']);

            return response()->json(['gridGroups' => $gridGroups, 'message' => 'Item da grade excluído'], 200);
        }, 'Erro ao excluir item da grade', $request);
    }
}
