<?php

namespace App\Services;

use App\DTO\Supplier\Order\CreateSupplierOrderDTO;
use App\DTO\Supplier\Order\Status\CreateSupplierOrderStatusHistoryDTO;
use App\DTO\Supplier\Order\Item\CreateSupplierOrderItemDTO;
use App\Repositories\SupplierOrderRepository;

class SupplierOrderService
{
    public function __construct(protected SupplierOrderRepository $repository) {}

    public function create($request)
    {
        $orderDTO = CreateSupplierOrderDTO::fromRequest($request);

        $order = $this->repository->create($orderDTO->toArray());

        // Cria itens do pedido
        $this->createOrderItems($order, $request->input('items'));

        // Criar status inicial do pedido
        $this->createOrderStatusHistory($order);
    }

    private function createOrderItems($order, array $items): void
    {
        $itemsToInsert = [];

        foreach ($items as $itemData) {
            $itemData['supplierOrderID'] = $order->id;
            $itemDTO = CreateSupplierOrderItemDTO::fromRequest($itemData);
            $itemsToInsert[] = $itemDTO->toArray();
        }
        $order->items()->createMany($itemsToInsert);
    }

    private function createOrderStatusHistory($order): void
    {
        $statusDTO = CreateSupplierOrderStatusHistoryDTO::start(orderID: $order->id);

        $order->status()->create($statusDTO->toArray());
    }
}
