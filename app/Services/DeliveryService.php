<?php

namespace App\Services;

use App\DTO\Delivery\CreateScheduleDeliveryDTO;
use App\DTO\Delivery\UpdateDeliveryDTO;
use App\DTO\Sale\SaleItem\UpdateSaleItemDTO;
use App\Helpers\DeliveryGuyHelper;
use App\Helpers\DeliveryHelper;
use App\Repositories\DeliveryGuyRepository;
use App\Repositories\SaleDeliveryRepository;
use App\Repositories\SaleItemRepository;
use App\Repositories\ReturnExchangeItemRepository;

class DeliveryService
{
    public function __construct(
        protected DeliveryGuyRepository $deliveryGuyRepository,
        protected SaleDeliveryRepository $repository,
        protected SaleItemRepository $saleItemRepository,
        protected ReturnExchangeItemRepository $returnExchangeItemRepository,
    ) {}

    public function schedule($request)
    {
        DeliveryGuyHelper::existsDeliveryGuy($request->deliveryGuyID);
        DeliveryHelper::existsDelivery($request->deliveryID);

        $deliveryGuy = null;

        if($request->deliveryGuyID){
            $deliveryGuy = $this->deliveryGuyRepository->findById($request->deliveryGuyID);
        }

        $scheduleDTO = CreateScheduleDeliveryDTO::fromRequest($request, $deliveryGuy?->name, $deliveryGuy?->phone);

        return $this->repository->update($request->deliveryID, $scheduleDTO->toArray());
    }   

    public function partialDelivered($request)
    {
        DeliveryHelper::checkDeliveredAndSaledQuantity($request['deliveryID'], $request['deliveredProducts']);
        $isStatusDelivered = DeliveryHelper::isStatusDelivered($request['deliveredProducts']);

        $this->updateSaleItens($request['deliveryID'],$request['deliveredProducts']);

        if($isStatusDelivered){
            $this->updateDelivery($request['deliveryID'], 'delivered');
        } else {
            $this->updateDelivery($request['deliveryID'], 'partial_delivered');
        }
    }

    private function updateSaleItens(int $deliveryID, array $deliveredProducts)
    {
        $delivery = $this->repository->findById($deliveryID);

        if($delivery->return_id){

            foreach($deliveredProducts as $data){
            $saleItemDTO = UpdateSaleItemDTO::fromRequest([
                'delivered' => $data['quantitySaled'] === $data['quantityDelivered'] ? 1 : 0,
                'quantityDelivered' => $data['quantityDelivered']
            ]);

            $this->returnExchangeItemRepository->updateByProductVariantID($data['productVariantID'], $delivery->return_id, $saleItemDTO->toArray());
        }

        } else {

            foreach($deliveredProducts as $data){
            $saleItemDTO = UpdateSaleItemDTO::fromRequest([
                'delivered' => $data['quantitySaled'] === $data['quantityDelivered'] ? 1 : 0,
                'quantityDelivered' => $data['quantityDelivered']
            ]);

            $this->saleItemRepository->updateByProductVariantID($data['productVariantID'], $delivery->sale_id, $saleItemDTO->toArray());
        }

        }

        return true;
    }

    private function updateDelivery(int $deliveryID, string $status)
    {
       $deliveryDTO = UpdateDeliveryDTO::fromRequest($status);

       return $this->repository->update($deliveryID, $deliveryDTO->toArray());
    }
}
