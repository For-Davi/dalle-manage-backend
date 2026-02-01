<?php

namespace App\Services;

use App\DTO\Exchange\CreateExchangeDTO;
use App\DTO\Exchange\UpdateExchangeDTO;
use App\DTO\Exchange\ExchangePayment\CreateExchangeChangeDTO;
use App\DTO\Exchange\ExchangePayment\CreateExchangePaymentMethodDTO;
use App\Repositories\SaleRepository;
use App\Repositories\ExchangeRepository;
use App\Repositories\ClientRepository;
use App\Repositories\ExchangePaymentMethodRepository;
use App\Repositories\ExchangeChangeRepository;
use App\Helpers\SaleHelper;
use App\Helpers\ExchangePaymentHelper;
use Illuminate\Support\Facades\Auth;

class ExchangeService
{
    public function __construct(
        protected ExchangeRepository $repository,
        protected SaleRepository $saleRepository,
        protected ClientRepository $clientRepository,
        protected ExchangePaymentMethodRepository $exchangePaymentRepository,
        protected ExchangeChangeRepository $exchangeChangeRepository,
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

    public function createPayment($request)
    {
        $enterpriseID = Auth::user()->enterprise_id;

        $this->validatePaymentData($request['exchangeData'], $enterpriseID);

        $this->savePaymentData($request['exchangeData'], $request['additionalExchangePaymentData']['exchangeID']);

        return $this->createChange($request['exchangeData']);
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

    private function createChange($changeData)
    {
        $changeDTO = CreateExchangeChangeDTO::fromRequest($changeData);

        return $this->exchangeChangeRepository->create($changeDTO->toArray());
    }

    private function savePaymentData($exchangeData, int $exchangeID)
    {
        foreach($exchangeData as $exchange){
            $paymentDTO = CreateExchangePaymentMethodDTO::fromRequest([
                'exchangeID' => $exchangeID,
                'receiptID' => $exchange['receiptID'],
                'receiptName' => $exchange['receiptName'],
                'paymentMethodID' => $exchange['paymentMethodID'],
                'value' => $exchange['value'],
            ]);

            $this->exchangePaymentRepository->create($paymentDTO->toArray());
        }

        return true;
    }

    private function validatePaymentData($exchangeData, int $enterpriseID)
    {
        foreach($exchangeData as $exchange){
            ExchangePaymentHelper::existsReceipt($exchange['receiptID']);
            ExchangePaymentHelper::findPaymentMethodID($exchange['typeReceipt'], $enterpriseID, $exchange['receiptID']);
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
