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

    public function update($request)
    {
        DeliveryHelper::existsDelivery($request->deliveryID);

        $delivery = $this->repository->findById($request->deliveryID);

        $deliveryGuy = null;

        if($request->deliveryStatus === 'delivered' && !$delivery->delivery_guy_id && !$delivery->delivery_guy_name){
            DeliveryGuyHelper::existsDeliveryGuy($request->deliveryGuyID);
            $deliveryGuy = $this->deliveryGuyRepository->findById($request->deliveryGuyID);
        }
        
        $this->updateItensWhenDelivered($delivery->sale_id, $delivery->return_id);

        $deliveryDTO = UpdateDeliveryDTO::fromRequest($request->deliveryStatus, $deliveryGuy);

        return $this->repository->update($request->deliveryID, $deliveryDTO->toArray());
    }

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
        DeliveryHelper::isAllZero($request['deliveredProducts']);
        DeliveryHelper::checkDeliveredAndSaledQuantity($request['deliveryID'], $request['deliveredProducts']);
        $isStatusDelivered = DeliveryHelper::isStatusDelivered($request['deliveredProducts']);

        $this->updateSaleItens($request['deliveryID'],$request['deliveredProducts']);

        if($isStatusDelivered){
            $this->updateDelivery($request['deliveryID'], 'delivered');
        } else {
            $this->updateDelivery($request['deliveryID'], 'partial_delivered');
        }
    }

    public function export($request)
    {
        // $dateTime = now()->format('Ymd_His');

        // $exportMovementDTO = FilterMovementDTO::fromRequest($request);
        // $movements = $this->repository->getAllWithFilter($exportMovementDTO->toArray(), ['category']);

        // if ($request->format === 'excel') {
        //     $fileName = "movements_{$dateTime}.xlsx";

        //     return (new MovementsExport($movements))->download($fileName);
        // } else {
        //     $fileName = "movements_{$dateTime}.xlsx";

        //     $pdf = Pdf::loadView('exports.movements-pdf', [
        //         'movements' => $movements,
        //     ]);

        //     return $pdf->download($fileName);
        // }
    }

    private function updateItensWhenDelivered(int $saleID, ?int $returnID)
    {   
        if($returnID){
            $products = $this->returnExchangeItemRepository->findByReturnId($returnID);

        foreach($products as $product){
            $saleItemDTO = UpdateSaleItemDTO::fromRequest([
                'delivered' => 1,
                'quantityDelivered' => $product->quantity
            ]);

            $this->returnExchangeItemRepository->updateByProductVariantID($product->product_variant_id, $returnID, $saleItemDTO->toArray());
        }
        } else {
            $products = $this->saleItemRepository->findBySaleId($saleID);

        foreach($products as $product){
            $saleItemDTO = UpdateSaleItemDTO::fromRequest([
                'delivered' => 1,
                'quantityDelivered' => $product->quantity
            ]);

            $this->saleItemRepository->updateByProductVariantID($product->product_variant_id, $saleID, $saleItemDTO->toArray());
        }
        }

        return true;
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
