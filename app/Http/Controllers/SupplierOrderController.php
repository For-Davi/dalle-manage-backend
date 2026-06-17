<?php

namespace App\Http\Controllers;

use App\Http\Requests\Supplier\Order\CreateSupplierOrderRequest;
use App\Http\Requests\Supplier\Order\DeleteSupplierOrderRequest;
use App\Http\Requests\Supplier\Order\ExportSupplierOrderRequest;
use App\Http\Requests\Supplier\Order\ShowSupplierOrderRequest;
use App\Http\Requests\Supplier\Order\UpdateSupplierOrderReceivedRequest;
use App\Http\Requests\Supplier\Order\UpdateSupplierOrderRequest;
use App\Http\Requests\Supplier\Order\UpdateSupplierOrderStatusRequest;
use App\Http\Resources\Supplier\Order\SupplierOrderListResource;
use App\Repositories\SupplierOrderItemRepository;
use App\Repositories\SupplierOrderRepository;
use App\Repositories\SupplierOrderStatusHistoryRepository;
use App\Services\SupplierOrderService;
use Illuminate\Http\Request;

class SupplierOrderController extends BaseController
{
    public function __construct(
        private SupplierOrderService $service,
        private SupplierOrderRepository $repository,
        private SupplierOrderItemRepository $orderItemRepository,
        private SupplierOrderStatusHistoryRepository $orderStatusHistoryRepository
    ) {}

    public function index(Request $request)
    {
        return $this->safeExecute(function () {
            $orders = $this->repository->getAllByEnterprise();

            return response()->json(['orders' => SupplierOrderListResource::collection($orders)], 200);
        }, 'Erro ao buscar pedidos', $request);
    }

    public function getHistory(ShowSupplierOrderRequest $request)
    {
        return $this->safeExecute(function () use ($request) {
            $history = $this->orderStatusHistoryRepository->getAllByEnterprise(
                ['order', 'changed'],
                ['*'],
                ['supplier_order_id' => $request->route('orderID')]
            );

            return response()->json(['history' => $history], 200);
        }, 'Erro ao buscar histórico de pedido', $request);
    }

    public function show(ShowSupplierOrderRequest $request)
    {
        return $this->safeExecute(function () use ($request) {
            $order = $this->repository->findById($request->route('orderID'), ['items.variant', 'items.variant.color', 'items.variant.gridItem', 'items.variant.product', 'user']);

            return response()->json(['order' => $order], 200);
        }, 'Erro ao buscar pedido', $request);
    }

    public function received(UpdateSupplierOrderReceivedRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            check_permission('supplier-order.update');
            $this->service->received($request);
            $item = $this->orderItemRepository->findById($request->items[0]['id']);
            $order = $this->repository->findById($item->supplier_order_id, ['items.variant', 'items.variant.color', 'items.variant.gridItem', 'items.variant.product', 'user']);

            return response()->json(['order' => $order, 'message' => 'Quantidade recebida de produtos atualizado no pedido'], 200);
        }, 'Erro ao atualizar quantidade recebida de item do pedido', $request);
    }

    public function store(CreateSupplierOrderRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            check_plan('supplier_orders');
            check_permission('supplier-order.create');
            $this->service->create($request);
            $orders = $this->repository->getAllByEnterprise();

            return response()->json(['orders' => SupplierOrderListResource::collection($orders), 'message' => 'Pedido cadastrado'], 201);
        }, 'Erro ao cadastrar pedido', $request);
    }

    public function update(UpdateSupplierOrderRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            check_permission('supplier-order.update');
            $this->service->update($request);
            $orders = $this->repository->getAllByEnterprise();

            return response()->json(['orders' => SupplierOrderListResource::collection($orders), 'message' => 'Pedido atualizado'], 200);
        }, 'Erro ao atualizar pedido', $request);
    }

    public function updateStatus(UpdateSupplierOrderStatusRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            check_permission('supplier-order.update');
            $this->service->updateStatus($request);
            $order = $this->repository->findById($request->id, ['items.variant', 'items.variant.color', 'items.variant.gridItem', 'items.variant.product', 'user']);

            return response()->json(['order' => $order, 'message' => 'Status de pedido atualizado'], 200);
        }, 'Erro ao atualizar status do pedido', $request);
    }

    public function export(ExportSupplierOrderRequest $request)
    {
        return $this->safeExecute(function () use ($request) {
            return $this->service->export($request);
        }, 'Erro ao exportar pedido', $request);
    }

    public function destroy(DeleteSupplierOrderRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            check_permission('supplier-order.delete');
            $this->repository->delete($request->route('orderID'));
            $orders = $this->repository->getAllByEnterprise();

            return response()->json(['orders' => SupplierOrderListResource::collection($orders), 'message' => 'Pedido excluído'], 200);
        }, 'Erro ao excluir pedido', $request);
    }
}
