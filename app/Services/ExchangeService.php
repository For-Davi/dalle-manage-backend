<?php

namespace App\Services;

use App\DTO\Exchange\CreateExchangeDTO;
use App\DTO\Exchange\ExchangePayment\CreateExchangeAdditionalDTO;
use App\DTO\Exchange\ExchangePayment\CreateExchangePaymentMethodDTO;
use App\DTO\Exchange\UpdateExchangeDTO;
use App\DTO\Sale\SaleDelivery\CreateSaleDeliveriesDTO;
use App\DTO\Sale\SalePayment\CreateSalePaymentDTO;
use App\Helpers\ClientHelper;
use App\Helpers\ExchangePaymentHelper;
use App\Helpers\SaleHelper;
use App\Jobs\Email\SendCouponToEmailJob;
use App\Repositories\ClientRepository;
use App\Repositories\ExchangeAdditionalRepository;
use App\Repositories\ExchangePaymentMethodRepository;
use App\Repositories\ExchangeRepository;
use App\Repositories\ReceiptRepository;
use App\Repositories\ReturnExchangeItemRepository;
use App\Repositories\ReturnRepository;
use App\Repositories\SaleDeliveryRepository;
use App\Repositories\SalePaymentsMethodRepository;
use App\Repositories\SaleRepository;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExchangeService
{
    public function __construct(
        protected ExchangeRepository $repository,
        protected SaleRepository $saleRepository,
        protected ClientRepository $clientRepository,
        protected ExchangePaymentMethodRepository $exchangePaymentRepository,
        protected ExchangeAdditionalRepository $exchangeAdditionalRepository,
        protected ReceiptRepository $receiptRepository,
        protected SalePaymentsMethodRepository $salePaymentsRepository,
        protected SaleDeliveryRepository $saleDeliveryRepository,
        protected ReturnExchangeItemRepository $returnExchangeItemRepository,
        protected ProductMovementService $productMovementService,
        protected CommissionService $commissionService,
        protected ReturnRepository $returnRepository,
    ) {}

    public function create(array $exchangeData, int $saleID, int $returnID)
    {
        SaleHelper::existsSale($saleID, Auth::user()->enterprise_id);

        $sale = $this->saleRepository->findById($saleID);

        $exchangeDTO = CreateExchangeDTO::fromRequest([
            'saleID' => $sale->id,
            'returnID' => $returnID,
            'exchangeValue' => $exchangeData['exchangeValue'],
            'differenceValue' => $exchangeData['differenceValue'],
        ]);

        return $this->repository->create($exchangeDTO->toArray());
    }

    public function createExchangePayment($request)
    {
        $enterpriseID = Auth::user()->enterprise_id;

        $this->saveExchangePaymentData($request['exchangePaymentData'], $request['additionalExchangePaymentData']['exchangeID'], $enterpriseID);

        $this->updateProductMovement($request['additionalExchangePaymentData']['exchangeID'], $enterpriseID);

        $this->createAdditionalData($request['additionalExchangePaymentData']);

        return $this->updateSale(
            $request['additionalExchangePaymentData']['exchangeID'],
            $request['additionalExchangePaymentData']['saleID'],
        );
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

        if ($request['differenceDeliveryData']['freight']) {
            $this->createDifferenceDeliveryData(
                $request['differenceDeliveryData'],
                $request['additionalDifferencePaymentData']['saleID'],
                $request['additionalDifferencePaymentData']['exchangeID']
            );
        }

        $this->updateProductMovement($request['additionalDifferencePaymentData']['exchangeID'], $enterpriseID);

        $sale = $this->saleRepository->findById($request['additionalDifferencePaymentData']['saleID']);
        $return = $this->returnRepository->findById($request['additionalDifferencePaymentData']['returnID']);

        if ($return->seller_id) {
            $returnExchangeItems = $this->returnExchangeItemRepository->findByReturnId($request['additionalDifferencePaymentData']['returnID']);

            return $this->commissionService->create($sale->id, $return->seller_id, $return->id, $returnExchangeItems->toArray());
        }

        $creditValue = $this->checkIfExistsCredit($request['differencePaymentData']);

        $this->updateSale(
            $request['additionalDifferencePaymentData']['exchangeID'],
            $request['additionalDifferencePaymentData']['saleID'],
            (float) $request['additionalDifferencePaymentData']['fees'],
            (float) $request['additionalDifferencePaymentData']['change'],
            (float) $creditValue
        );

        return true;
    }

    public function updateExchangeAfterReturn($request)
    {

        $sale = $this->saleRepository->findById($request['saleID']);
        $exchange = $this->repository->findByReturnId($request['id']);

        if ($exchange) {
            $exchangeAdditional = $this->exchangeAdditionalRepository->findByExchangeId($exchange->id);
            $exchangeDTO = UpdateExchangeDTO::fromRequest($request);
            $updatedExchange = $this->repository->update($exchange->id, $exchangeDTO->toArray());
            $currentTotal = $this->getCurrentTotal($updatedExchange, $sale, $exchangeAdditional);

            return $this->saleRepository->update($sale->id, ['current_total' => $currentTotal]);
        } else {
            return $this->updateClientCredit($sale->client_id);
        }
    }

    public function export($request)
    {
        $dateTime = now()->format('Ymd_His');
        $couponData = $this->repository->getCouponInfos($request->exchangeID);

        if ($couponData) {
            $fileName = "cupom_fiscal_{$dateTime}.pdf";

            $pdf = Pdf::loadView('exports.exchange-tax-coupon-pdf', [
                'couponData' => $couponData,
            ]);

            return $pdf->download($fileName);
        }
    }

    public function sendToEmail($request)
    {
        if ($request->email) {

            $couponData = $this->repository->getCouponInfos($request->exchangeID);

            SendCouponToEmailJob::dispatch($request->email, $couponData, 'exchange');
        }

        return 'O cupom será enviado ao e-mail informado';
    }

    private function updateClientCredit(int $clientID, ?float $creditValue = null)
    {
        if ($clientID) {
            ClientHelper::existsClient($clientID, 'saleID');
            if ($creditValue) {
                ClientHelper::validateCredit($clientID, $creditValue, 'saleID');
            }

            return $this->clientRepository->update($clientID, ['credits' => 0, 'credit_expires_at' => null]);
        }

        return true;
    }

    private function checkIfExistsCredit(array $payments)
    {
        foreach ($payments as $payment) {
            if ($payment['paymentType'] === 'CREDIT') {
                return $payment['value'];
            }
        }

        return null;
    }

    private function updateSale(int $exchangeID, int $saleID, ?float $fees = null, ?float $change = null, ?float $creditValue = null)
    {
        $exchange = $this->repository->findById($exchangeID);

        if ($exchange) {
            $sale = $this->saleRepository->findById($saleID);

            $currentTotal = $exchange->exchange_value > 0 ? $sale->current_total - $exchange->exchange_value : $sale->current_total + $exchange->difference_value;
            if ($change) {
                $currentTotal -= $change;
            }
            if ($fees) {
                $currentTotal += $fees;
            }
            if ($creditValue) {
                $currentTotal -= $creditValue;
            }

            return $this->saleRepository->update($sale->id, ['current_total' => $currentTotal]);
        }

        return true;
    }

    private function updateProductMovement(int $exchangeID, int $enterpriseID)
    {
        $exchange = $this->repository->findById($exchangeID);

        $returnExchangeProducts = $this->returnExchangeItemRepository->findByReturnId($exchange->return_id);

        if ($returnExchangeProducts) {
            foreach ($returnExchangeProducts as $product) {
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
                    'returnID' => $exchange->return_id,
                ];

                $this->productMovementService->create(new Request($movementData));
            }
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
        if ($additionalData['change'] > 0 || (float) $additionalData['fees'] > 0 || $additionalData['description']) {
            $changeDTO = CreateExchangeAdditionalDTO::fromRequest($additionalData);

            $this->exchangeAdditionalRepository->create($changeDTO->toArray());
        }

        return true;
    }

    private function saveExchangePaymentData($exchangeData, int $exchangeID, int $enterpriseID)
    {
        foreach ($exchangeData as $exchange) {
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
            if ($payment['paymentType'] !== 'CREDIT') {
                ExchangePaymentHelper::existsReceipt($payment['receiptID']);
            }

            $paymentMethodID = ExchangePaymentHelper::findPaymentMethodID($payment['paymentType'], $enterpriseID, $payment['receiptID']);

            $installment = $payment['installment'] ?? ['value' => null, 'amount' => null];

            $isValidInstallment =
                isset($installment['value'], $installment['amount']) &&
                $installment['value'] >= 1 &&
                $installment['value'] <= 12 &&
                $installment['amount'] > 0;

            $installments = $isValidInstallment ? $installment['value'] : null;
            $amount = $isValidInstallment ? $installment['amount'] : $payment['value'];

            if ($payment['paymentType'] !== 'CREDIT') {
                $receipt = $this->receiptRepository->findById($payment['receiptID']);
            }

            $salePaymentDTO = CreateSalePaymentDTO::fromRequest([
                'saleID' => $saleID,
                'exchangeID' => $exchangeID,
                'paymentMethodID' => $paymentMethodID,
                'receiptID' => $payment['receiptID'],
                'receiptName' => $receipt->identifier ?? null,
                'installments' => $installments,
                'value' => $amount,
            ]);

            $this->salePaymentsRepository->create($salePaymentDTO->toArray());
        }

        return true;
    }

    private function getCurrentTotal($exchange, $sale, $additionalExchange)
    {
        if ($exchange->status === 'active' && $exchange->exchange_value > 0) {
            return $sale->current_total - $exchange->exchange_value;
        }
        if ($exchange->status === 'active' && $exchange->difference_value > 0) {
            return $sale->current_total + ($exchange->difference_value + ($additionalExchange->fees ?? 0) - ($additionalExchange->change ?? 0));
        }
        if ($exchange->status === 'canceled' && $exchange->exchange_value > 0) {
            return $sale->current_total + $exchange->exchange_value;
        }
        if ($exchange->status === 'canceled' && $exchange->difference_value > 0) {
            return $sale->current_total - ($exchange->difference_value + ($additionalExchange->fees ?? 0) - ($additionalExchange->change ?? 0));
        }
    }
}
