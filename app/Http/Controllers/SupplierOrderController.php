<?php

namespace App\Http\Controllers;

use App\Http\Requests\Supplier\Order\CreateSupplierOrderRequest;
use App\Http\Requests\Supplier\Order\DeleteSupplierOrderRequest;
use App\Http\Requests\Supplier\Order\ExportSupplierOrderRequest;
use App\Http\Requests\Supplier\Order\ShowSupplierOrderRequest;
use App\Http\Requests\Supplier\Order\UpdateSupplierOrderRequest;
use App\Http\Resources\Supplier\Order\SupplierOrderListResource;
use App\Repositories\SupplierOrderRepository;
use App\Services\SupplierOrderService;
use App\Utils\ErrorLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierOrderController
{
    public function __construct(
        private SupplierOrderService $service,
        private SupplierOrderRepository $repository
    ) {}

    public function index(Request $request)
    {
        try {
            $orders = $this->repository->getAllByEnterprise();

            return response()->json(['orders' => SupplierOrderListResource::collection($orders)], 200);
        } catch (\Exception $e) {
            ErrorLogger::log('Erro ao buscar pedidos:', $e, $request);

            return response()->json(['message' => 'Erro ao buscar pedidos'], 500);
        }
    }

    public function show(ShowSupplierOrderRequest $request)
    {
        try {
            $order = $this->repository->findById($request->route('orderID'), ['items.variant', 'items.variant.color', 'items.variant.gridItem', 'items.variant.product', 'user']);

            return response()->json(['order' => $order], 200);

        } catch (\Exception $e) {
            ErrorLogger::log('Erro ao buscar pedido:', $e, $request);

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function store(CreateSupplierOrderRequest $request)
    {
        try {
            DB::beginTransaction();
            $order = $this->service->create($request);

            if ($order) {
                DB::commit();
                $orders = $this->repository->getAllByEnterprise();

                return response()->json(['orders' => SupplierOrderListResource::collection($orders), 'message' => 'Pedido cadastrado'], 201);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            ErrorLogger::log('Erro ao cadastrar pedido:', $e, $request);

            return response()->json(['message' => 'Erro ao cadastrar pedido'], 500);
        }
    }

    public function update(UpdateSupplierOrderRequest $request)
    {
        try {
            DB::beginTransaction();
            $order = $this->service->update($request);

            if ($order) {
                DB::commit();

                $orders = $this->repository->getAllByEnterprise();

                return response()->json(['orders' => SupplierOrderListResource::collection($orders), 'message' => 'Pedido atualizado'], 200);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            ErrorLogger::log('Erro ao atualizar pedido:', $e, $request);

            return response()->json(['message' => 'Erro ao atualizar pedido'], 500);
        }
    }

    public function export(ExportSupplierOrderRequest $request)
    {
        try {
            return $this->service->export($request);
        } catch (\Exception $e) {

            ErrorLogger::log('Erro ao exportar pedido:', $e, $request);

            return response()->json(['message' => 'Erro ao exportar pedido'], 500);
        }
    }

    public function destroy(DeleteSupplierOrderRequest $request)
    {
        try {
            DB::beginTransaction();

            $order = $this->repository->delete($request->route('orderID'));

            if ($order) {
                DB::commit();
                $orders = $this->repository->getAllByEnterprise();

                return response()->json(['orders' => SupplierOrderListResource::collection($orders), 'message' => 'Pedido excluído'], 200);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            ErrorLogger::log('Erro ao excluir pedido:', $e, $request);

            return response()->json(['message' => 'Erro ao excluir pedido'], 500);
        }
    }
}
