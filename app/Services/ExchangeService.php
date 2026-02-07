<?php

namespace App\Services;

use App\DTO\Exchange\CreateExchangeDTO;
use App\DTO\Exchange\UpdateExchangeDTO;
use App\DTO\Exchange\ExchangePayment\CreateExchangeAdditionalDTO;
use App\DTO\Exchange\ExchangePayment\CreateExchangePaymentMethodDTO;
use App\DTO\Sale\SalePayment\CreateSalePaymentDTO;
use App\DTO\Sale\SaleDelivery\CreateSaleDeliveriesDTO;
use App\Repositories\SaleRepository;
use App\Repositories\ExchangeRepository;
use App\Repositories\ClientRepository;
use App\Repositories\ExchangePaymentMethodRepository;
use App\Repositories\ExchangeChangeRepository;
use App\Repositories\ReceiptRepository;
use App\Repositories\SalePaymentsMethodRepository;
use App\Repositories\SaleDeliveryRepository;
use App\Repositories\ReturnExchangeItemRepository;
use App\Services\ProductMovementService;
use App\Helpers\SaleHelper;
use App\Helpers\ExchangePaymentHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ExchangeService
{
    public function __construct(
        protected ExchangeRepository $repository,
        protected SaleRepository $saleRepository,
        protected ClientRepository $clientRepository,
        protected ExchangePaymentMethodRepository $exchangePaymentRepository,
        protected ExchangeChangeRepository $exchangeChangeRepository,
        protected ReceiptRepository $receiptRepository,
        protected SalePaymentsMethodRepository $salePaymentsRepository,
        protected SaleDeliveryRepository $saleDeliveryRepository,
        protected ReturnExchangeItemRepository $returnExchangeItemRepository,
        protected ProductMovementService $productMovementService,
    ) {}

    public function create(array $exchangeData, int $saleID, int $returnID)
    {
        SaleHelper::existsSale($saleID, Auth::user()->enterprise_id);

        $sale = $this->saleRepository->findById($saleID);

       $exchangeDTO = CreateExchangeDTO::fromRequest([
            'saleID' => $saleID,
            'returnID' => $returnID,
            'exchangeValue' => $exchangeData['exchangeValue'],
            'differenceValue' => $exchangeData['differenceValue'],
        ]);

        if($exchangeData['exchangeValue'] > 0){
            $currentTotal = $sale->current_total - $exchangeData['exchangeValue'];
        } else {
            $currentTotal = $sale->current_total + $exchangeData['differenceValue'];
        }

        $this->saleRepository->update($saleID, ['current_total' => $currentTotal]);

        return $this->repository->create($exchangeDTO->toArray());
    }

    public function createExchangePayment($request)
    {
        $enterpriseID = Auth::user()->enterprise_id;
    
        $this->saveExchangePaymentData($request['exchangePaymentData'], $request['additionalExchangePaymentData']['exchangeID'], $enterpriseID);

        return $this->createAdditionalData($request['additionalExchangePaymentData']);
    }

    public function createDifferencePayment($request)
    {
        $enterpriseID = Auth::user()->enterprise_id;

        $this->savePaymentDifferenceData(
            $request['differencePaymentData'], 
            $request['additionalDifferencePaymentData']['exchangeID'], 
            $request['additionalDifferencePaymentData']['saleID'], 
            $enterpriseID);

        $this->createAdditionalData($request['additionalDifferencePaymentData']);

        if($request['differenceDeliveryData']['freight']){
            $this->createDifferenceDeliveryData(
            $request['differenceDeliveryData'], 
            $request['additionalDifferencePaymentData']['saleID'], 
            $request['additionalDifferencePaymentData']['exchangeID'] 
            );
        }

        return $this->updateProductMovement($request['additionalDifferencePaymentData']['exchangeID'], $enterpriseID);
    }

    public function updateExchangeAfterReturn($request)
    {
        
        $sale = $this->saleRepository->findById($request['saleID']);
        $exchange =  $this->repository->findByReturnId($request['id']);

        if($exchange){
        $exchangeDTO = UpdateExchangeDTO::fromRequest($request);
        $updatedExchange = $this->repository->update($exchange->id, $exchangeDTO->toArray());
        $currentTotal = $this->getCurrentTotal($updatedExchange, $sale);

        return $this->saleRepository->update($sale->id, ['current_total' => $currentTotal]);
        } else {
            return $this->updateClientCredit($sale);
        }
    }


    private function updateProductMovement(int $exchangeID, int $enterpriseID)
    {
        $exchange = $this->repository->findById($exchangeID);

        $returnExchangeProducts = $this->returnExchangeItemRepository->findByReturnId($exchange->id);

        foreach($returnExchangeProducts as $product){
            $movementData = [
                'reason' => 'sale',
                'type' => 'out',
                'documentNumber' => null,
                'lotNumber' => null,
                'quantity' => $product->quantity,
                'unitCost' => null,
                'totalCost' => null,
                'variantID' => $product->product_variant_id,
                'supplierID' => null,
                'description' => null,
                'enterprise_id' => $enterpriseID,
            ];

            $this->productMovementService->create(new Request($movementData));
        }

        return true;
    }

    private function createDifferenceDeliveryData($deliveryData, int $saleID, int $exchangeID)
    {
        $deliveryDTO = CreateSaleDeliveriesDTO::fromRequest($deliveryData, $saleID, $exchangeID);
        $this->saleDeliveryRepository->create($deliveryDTO->toArray());
    }

    private function createAdditionalData($additionalData)
    {
        if($additionalData['change'] > 0 || $additionalData['description']){
            $changeDTO = CreateExchangeAdditionalDTO::fromRequest($additionalData);

            return $this->exchangeChangeRepository->create($changeDTO->toArray());
        }

        return true;
    }

    private function saveExchangePaymentData($exchangeData, int $exchangeID, int $enterpriseID)
    {
        foreach($exchangeData as $exchange){
            ExchangePaymentHelper::existsReceipt($exchange['receiptID']);
            $paymentMethodID = ExchangePaymentHelper::findPaymentMethodID($exchange['paymentType'], $enterpriseID, $exchange['receiptID']);

            $receipt = $this->receiptRepository->findById($exchange['receiptID']);

            $paymentDTO = CreateExchangePaymentMethodDTO::fromRequest([
                'exchangeID' => $exchangeID,
                'receiptID' => $exchange['receiptID'],
                'receiptName' => $receipt->identifier,
                'paymentMethodID' => $paymentMethodID,
                'value' => $exchange['value'],
            ]);

            $this->exchangePaymentRepository->create($paymentDTO->toArray());
        }

        return true;
    }

    private function savePaymentDifferenceData($exchangeData, int $exchangeID, int $saleID, int $enterpriseID)
    {
        foreach ($exchangeData as $payment) {
            ExchangePaymentHelper::existsReceipt($payment['receiptID']);
            $paymentMethodID = ExchangePaymentHelper::findPaymentMethodID($payment['paymentType'], $enterpriseID, $payment['receiptID']);

            $installment = $payment['installment'] ?? ['value' => null, 'amount' => null];

            $isValidInstallment =
                isset($installment['value'], $installment['amount']) &&
                $installment['value'] >= 1 &&
                $installment['value'] <= 12 &&
                $installment['amount'] > 0;

            $installments = $isValidInstallment ? $installment['value'] : null;
            $amount = $isValidInstallment ? $installment['amount'] : $payment['value'];

            $receipt = $this->receiptRepository->findById($payment['receiptID']);

            $salePaymentDTO = CreateSalePaymentDTO::fromRequest([
                'saleID' => $saleID,
                'exchangeID' => $exchangeID,
                'paymentMethodID' => $paymentMethodID,
                'receiptID' => $payment['receiptID'],
                'receiptName' => $receipt->identifier,
                'installments' => $installments,
                'value' => $amount,
            ]);

            $this->salePaymentsRepository->create($salePaymentDTO->toArray());
        }

        return true;
    }

    private function validatePaymentData($exchangeData, int $enterpriseID)
    {
        foreach($exchangeData as $exchange){
            ExchangePaymentHelper::existsReceipt($exchange['receiptID']);
            ExchangePaymentHelper::findPaymentMethodID($exchange['paymentType'], $enterpriseID, $exchange['receiptID']);
        }
    }

    private function updateClientCredit($sale)
    {
        if($sale->client_id){
            $client = $this->clientRepository->findById($sale->client_id);

        return $this->clientRepository->update($client->id, ['credit' => null]);
        } 
        return true;
    }

    private function getCurrentTotal($exchange, $sale)
    {
        if($exchange->status === 'active' && $exchange->exchange_value > 0){
            return $sale->current_total - $exchange->exchange_value;
        }
        if($exchange->status === 'active' && $exchange->difference_value > 0){
            return $sale->current_total + $exchange->difference_value;
        }
        if($exchange->status === 'canceled' && $exchange->exchange_value > 0){
            return $sale->current_total + $exchange->exchange_value;
        }
        if($exchange->status === 'canceled' && $exchange->difference_value > 0){
            return $sale->current_total - $exchange->difference_value;
        }
    }
}
