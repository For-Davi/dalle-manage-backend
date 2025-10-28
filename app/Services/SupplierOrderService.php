<?php

namespace App\Services;

use App\DTO\Supplier\Order\CreateSupplierOrderDTO;
use App\DTO\Supplier\Order\Item\CreateSupplierOrderItemDTO;
use App\DTO\Supplier\Order\Status\CreateSupplierOrderStatusHistoryDTO;
use App\DTO\Supplier\Order\UpdateSupplierOrderDTO;
use App\Repositories\SupplierOrderRepository;

class SupplierOrderService
{
    public function __construct(protected SupplierOrderRepository $repository) {}

    public function create($request)
    {
        $orderDTO = CreateSupplierOrderDTO::fromRequest($request);

        $order = $this->repository->create($orderDTO->toArray());

        $this->createOrderItems($order, $request->input('items'));
        $this->createOrderStatusHistory($order);
    }

    public function update($request)
    {
        $orderDTO = UpdateSupplierOrderDTO::fromRequest($request);

        $order = $this->repository->update($request->input('id'), $orderDTO->toArray());

        $this->syncOrderItems($order, $request->input('items'), $request->input('itemsToDelete'));
    }

    private function syncOrderItems($order, array $items, array $itemsToDelete = []): void
    {
        $order->items()->whereIn('id', $itemsToDelete)->delete();

        foreach ($items as $itemData) {
            $itemData['supplierOrderID'] = $order->id;

            $itemDTO = CreateSupplierOrderItemDTO::fromRequest($itemData);
            $order->items()->create($itemDTO->toArray());
        }
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
