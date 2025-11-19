<?php

namespace App\Services;

use App\DTO\Supplier\Order\CreateSupplierOrderDTO;
use App\DTO\Supplier\Order\CreateSupplierOrderReceivingDTO;
use App\DTO\Supplier\Order\Item\CreateSupplierOrderItemDTO;
use App\DTO\Supplier\Order\Item\UpdateSupplierOrderItemReceivedDTO;
use App\DTO\Supplier\Order\Status\CreateSupplierOrderStatusHistoryDTO;
use App\DTO\Supplier\Order\Status\UpdateSupplierOrderStatusDTO;
use App\DTO\Supplier\Order\UpdateSupplierOrderDTO;
use App\Helpers\SupplierOrderHelper;
use App\Repositories\SupplierOrderItemRepository;
use App\Repositories\SupplierOrderReceivingRepository;
use App\Repositories\SupplierOrderRepository;
use Barryvdh\DomPDF\Facade\Pdf;

class SupplierOrderService
{
    public function __construct(protected SupplierOrderRepository $repository, protected SupplierOrderReceivingRepository $orderReceivingRepository, protected SupplierOrderItemRepository $orderItemRepository) {}

    public function create($request)
    {
        $orderDTO = CreateSupplierOrderDTO::fromRequest($request);

        $order = $this->repository->create($orderDTO->toArray());

        $this->createOrderItems($order, $request->items);
        $this->createOrderStatusHistory($order);

        return $order;
    }

    public function update($request)
    {
        $orderDTO = UpdateSupplierOrderDTO::fromRequest($request);

        $order = $this->repository->update($request->id, $orderDTO->toArray());

        $this->syncOrderItems($order, $request->items, $request->itemsToDelete);

        return $order;
    }

    public function updateStatus($request)
    {
        $orderDTO = UpdateSupplierOrderStatusDTO::fromRequest($request);

        $order = $this->repository->update($request->id, $orderDTO->toArray());

        $this->createOrderStatusHistory($order, $request->status);

        return $order;
    }

    public function received($request)
    {
        foreach ($request->items as $item) {
            SupplierOrderHelper::verifyQuantityReceived($item['id'], $item['received']);

            $receivedDTO = UpdateSupplierOrderItemReceivedDTO::fromRequest($item);
            $this->orderItemRepository->update($item['id'], $receivedDTO->toArray());

            $receivingDTO = CreateSupplierOrderReceivingDTO::fromRequest([
                'supplierOrderItemID' => $item['id'],
                'quantityReceived' => $item['received'],
                'receivingDate' => $request->dateReceived,
            ]);
            $this->orderReceivingRepository->create($receivingDTO->toArray());
        }

        return true;
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

    private function createOrderStatusHistory($order, $status = 'waiting'): void
    {
        $statusDTO = CreateSupplierOrderStatusHistoryDTO::fromRequest([
            'orderID' => $order->id,
            'status' => $status,
        ]);

        $order->status()->create($statusDTO->toArray());
    }

    public function export($request)
    {
        $dateTime = now()->format('Ymd_His');

        $order = $this->repository->findById($request->orderID, ['items.variant', 'items.variant.color', 'items.variant.gridItem', 'items.variant.product', 'user']);

        $fileName = "order_{$dateTime}.xlsx";

        $pdf = Pdf::loadView('exports.order-pdf', [
            'order' => $order,
        ]);

        return $pdf->download($fileName);
    }
}
