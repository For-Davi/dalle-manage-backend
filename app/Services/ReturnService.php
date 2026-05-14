<?php

namespace App\Services;

use App\DTO\Exchange\ExchangePayment\CreateExchangePaymentMethodDTO;
use App\DTO\Return\CreateReturnDTO;
use App\DTO\Return\ExchangeReturnItem\CreateExchangeReturnItemDTO;
use App\DTO\Return\ReturnItem\CreateReturnItemDTO;
use App\DTO\Return\UpdateReturnDTO;
use App\DTO\Sale\SaleDelivery\CreateSaleDeliveriesDTO;
use App\DTO\Sale\SalePayment\CreateSalePaymentDTO;
use App\DTO\StockReentry\CreateStockReentryReturnItemsDTO;
use App\Helpers\ExchangeHelper;
use App\Helpers\ExchangePaymentHelper;
use App\Helpers\ProductVariantHelper;
use App\Helpers\ReturnHelper;
use App\Helpers\ReturnItemHelper;
use App\Helpers\SaleHelper;
use App\Helpers\StockReentryReturnItemHelper;
use App\Jobs\Email\SendCouponToEmailJob;
use App\Repositories\ClientRepository;
use App\Repositories\EmployeeRepository;
use App\Repositories\ExchangePaymentMethodRepository;
use App\Repositories\ProductMovementRepository;
use App\Repositories\ReceiptRepository;
use App\Repositories\ReturnExchangeItemRepository;
use App\Repositories\ReturnItemRepository;
use App\Repositories\ReturnRepository;
use App\Repositories\SaleDeliveryRepository;
use App\Repositories\SalePaymentsMethodRepository;
use App\Repositories\SaleRepository;
use App\Repositories\StockReentryReturnItemRepository;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReturnService
{
    public function __construct(
        protected ReturnRepository $repository,
        protected ReturnItemRepository $returnItemRepository,
        protected ReturnExchangeItemRepository $returnExchangeItemRepository,
        protected ExchangePaymentMethodRepository $exchangePaymentMethodRepository,
        protected SalePaymentsMethodRepository $salePaymentsMethodRepository,
        protected ClientService $clientService,
        protected ProductMovementService $productMovementService,
        protected StockReentryReturnItemRepository $stockReentryReturnItemRepository,
        protected ProductMovementRepository $productMovementRepository,
        protected EmployeeRepository $employeeRepository,
        protected ReceiptRepository $receiptRepository,
        protected SaleRepository $saleRepository,
        protected SaleDeliveryRepository $saleDeliveryRepository,
        protected ClientRepository $clientRepository,
    ) {}

    public function create($request)
    {
        $enterpriseID = Auth::user()->enterprise_id;

        // Verificação da venda
        SaleHelper::existsSale($request['saleID'], $enterpriseID);

        // Verificação dos produtos da devolução
        if (! $request['returnID']) {
            $this->validateReturnProducts($request['returnData'], $request['saleID']);
        }

        // Verificação dos produtos da vinculação de devolução(caso tenha)
        if ($request['returnID']) {
            $this->validateVinculateReturnProducts($request['returnData'], $request['returnID'], $request['saleID']);
        }

        // Verificação dos produtos da troca (caso tenha)
        $this->validateExchangeProducts($request['exchangeProducts']);

        // Verificação do valor do exchange e do difference
        $this->validateExchangeAndDifference($request);

        // Criação da devolução
        $return = $this->createReturn($request);

        // Criação dos itens da devolução
        $this->createReturnItems($request['returnData'], $return->id);

        // Criação dos itens de troca e movimentação (caso tenha)
        if ($request['exchangeProducts']) {
            $this->createExchangeReturnItems($request['exchangeProducts'], $return->id, $request['paymentData']['deliveryData']['freight']);
            $this->createReturnExchangeProductMovement($return->id, $enterpriseID, 'trade', 'out');
        }

        // Criação da entrega da devolução (caso tenha)
        if ($request['paymentData']['deliveryData']['freight']) {
            $this->createReturnDelivery($request['paymentData']['deliveryData'], $request['paymentData']['freightPaymentData']['fees'], $return->sale_id, $return->id);
        }

        // Validação se gera crédito
        $createCredit = $request['exchangeData']['generatesCredit'] === 1 && $request['exchangeData']['exchangeValue'] > 0 && $request['exchangeData']['differenceValue'] === 0;

        // Gera crédito ao cliente
        if ($createCredit) {
            return $this->clientService->updateCredit($request['saleID'], $request['exchangeData']['exchangeValue'], null, 'exchangeData', 'increase');
        }

        // Cria pagamento do estorno/diferença/frete (caso tenha)
        return $this->createPayment($request, $request['saleID'], $return->id, $enterpriseID);
    }

    public function update($request)
    {
        $enterpriseID = Auth::user()->enterprise_id;

        ReturnHelper::existsReturn($request->saleID, $request->id);
        ReturnHelper::isSameStatus($request->id, $request->status);

        $returnDTO = UpdateReturnDTO::fromRequest($request);

        $return = $this->repository->update($request->id, $returnDTO->toArray());

        $this->updateSaleCurrentTotal($return->sale_id, $return);

        $this->updateStockReentryReturnItem($return->id, $request->status, $enterpriseID);

        return $this->updateReturnExchangeProductMovement($return->id, $request->status);
    }

    public function export($request)
    {
        $dateTime = now()->format('Ymd_His');
        $couponData = $this->repository->findById($request->returnID, ['sale.enterprise', 'returnExchangeItems', 'delivery']);

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

            $couponData = $this->repository->findById($request->returnID, ['sale.enterprise', 'returnExchangeItems', 'delivery']);

            SendCouponToEmailJob::dispatch($request->email, $couponData, 'exchange');
        }

        return 'O cupom será enviado ao e-mail informado';
    }

    private function updateSaleCurrentTotal(int $saleID, $return)
    {
        $sale = $this->saleRepository->findById($saleID);

        $exchangePaymentsMethods = $this->exchangePaymentMethodRepository->findByReturnId($return->id);

        $currentTotal = $sale->current_total;

        if ($return->exchange_value > 0 && $exchangePaymentsMethods->isNotEmpty()) {
            $currentTotal += $return->current_value;
        }
        if ($return->difference_value > 0) {
            $currentTotal -= $return->current_value;
        }
        if ($return->exchange_value > 0 && $exchangePaymentsMethods->isEmpty()) {
            $client = $this->clientRepository->findById($sale->client_id);

            return $this->clientService->updateCredit(null, $client->credits, $client->id, 'status', 'decrease');
        }
        if ($currentTotal < 0) {
            $currentTotal = 0;
        }

        return $this->saleRepository->update($saleID, ['current_total' => $currentTotal]);
    }

    private function createReturnDelivery($delivery, $fees, int $saleID, int $returnID)
    {
        $deliveryDTO = CreateSaleDeliveriesDTO::fromRequest($delivery, $saleID, $returnID, $fees);
        $this->saleDeliveryRepository->create($deliveryDTO->toArray());
    }

    private function validateExchangeAndDifference($request)
    {
        ExchangeHelper::validateExchangeAndDifference($request['exchangeData']['exchangeValue'], $request['exchangeData']['differenceValue']);
    }

    private function createPayment($request, int $saleID, int $returnID, int $enterpriseID)
    {
        if ($request['exchangeData']['exchangeValue'] > 0) {
            $this->createExchangePayment($request['paymentData']['paymentExchangeOrDifferenceData']['payment'], $returnID, $enterpriseID);
        }

        if ($request['exchangeData']['differenceValue'] > 0) {
            $this->createDifferenceOrFreightPayment($request['paymentData']['paymentExchangeOrDifferenceData']['payment'], $saleID, $returnID, $enterpriseID);
            $this->clientCreditAndCurrentValueUpdate($request['paymentData']['paymentExchangeOrDifferenceData']['payment'], $saleID, $returnID, 'paymentData.paymentExchangeOrDifferenceData.payment.*.value');

            return $this->repository->findById($returnID, ['sale.enterprise', 'returnExchangeItems', 'delivery']);
        }

        if ($request['exchangeData']['differenceValue'] === 0 && $request['paymentData']['deliveryData']['freight'] && $request['paymentData']['deliveryData']['freightValue'] > 0) {
            $this->createDifferenceOrFreightPayment($request['paymentData']['freightPaymentData']['payment'], $saleID, $returnID, $enterpriseID);
            $this->clientCreditAndCurrentValueUpdate($request['paymentData']['freightPaymentData']['payment'], $saleID, $returnID, 'paymentData.freightPaymentData.payment.*.value');

            return $this->repository->findById($returnID, ['sale.enterprise', 'returnExchangeItems', 'delivery']);
        }

        return true;
    }

    private function clientCreditAndCurrentValueUpdate($payments, $saleID, $returnID, $errorField)
    {
        $creditValue = $this->checkIfExistsCredit($payments);

        if ($creditValue) {
            $this->clientService->updateCredit($saleID, $creditValue, null, $errorField, 'decrease');

            return $this->updateCurrentValue($returnID, $creditValue);
        }

        return true;
    }

    private function updateCurrentValue(int $returnID, $credit)
    {
        $return = $this->repository->findById($returnID);

        $currentValue = $return->current_value - $credit;

        return $this->repository->update($returnID, ['current_value' => $currentValue]);
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

    private function createExchangePayment($payments, int $returnID, int $enterpriseID)
    {
        foreach ($payments as $payment) {
            ExchangePaymentHelper::existsReceipt($payment['receiptID']);
            $paymentMethodID = ExchangePaymentHelper::findPaymentMethodID($payment['paymentType'], $enterpriseID, $payment['receiptID']);

            $receipt = $this->receiptRepository->findById($payment['receiptID']);

            $paymentDTO = CreateExchangePaymentMethodDTO::fromRequest($payment, $returnID, $receipt->identifier, $paymentMethodID);

            $this->exchangePaymentMethodRepository->create($paymentDTO->toArray());
        }

        return true;
    }

    private function createDifferenceOrFreightPayment($payments, int $saleID, int $returnID, int $enterpriseID)
    {
        foreach ($payments as $payment) {

            if ($payment['paymentType'] !== 'CREDIT') {
                SaleHelper::existsReceipt($payment['receiptID']);
            }
            $paymentMethodID = SaleHelper::findPaymentMethodID($payment['paymentType'], $enterpriseID, $payment['receiptID']);

            $receipt = $this->receiptRepository->findById($payment['receiptID']);

            $installment = $payment['installment'] ?? ['value' => null, 'amount' => null];

            $isValidInstallment =
                isset($installment['value'], $installment['amount']) &&
                $installment['value'] >= 1 &&
                $installment['value'] <= 12 &&
                $installment['amount'] > 0;

            $installments = $isValidInstallment ? $installment['value'] : null;
            $amount = $isValidInstallment ? $installment['amount'] : $payment['value'];

            $paymentDTO = CreateSalePaymentDTO::fromRequest($payment, $saleID, $returnID, $receipt->identifier, $paymentMethodID, $installments, $amount);

            $this->salePaymentsMethodRepository->create($paymentDTO->toArray());
        }

        return true;
    }

    private function createReturn($request)
    {
        $seller = null;
        $exchangeOrDifferenceCurrentValue = 0;

        if ($request['sellerID'] && $request['exchangeProducts']) {
            $seller = $this->employeeRepository->findById($request['sellerID']);
        }
        if ($request['exchangeData']['exchangeValue'] > 0 && ! $request['exchangeData']['generatesCredit']) {
            $exchangeOrDifferenceCurrentValue += $request['exchangeData']['exchangeValue'];
        }
        if ($request['exchangeData']['differenceValue'] > 0 && ! $request['exchangeData']['generatesCredit']) {
            $exchangeOrDifferenceCurrentValue += $request['exchangeData']['differenceValue'];
            $exchangeOrDifferenceCurrentValue += $request['paymentData']['deliveryData']['freightValue'];
        }

        $change = $request['paymentData']['freightPaymentData']['change'] > 0 ? $request['paymentData']['freightPaymentData']['change'] : $request['paymentData']['paymentExchangeOrDifferenceData']['change'];
        $fees = $request['paymentData']['freightPaymentData']['fees'] > 0 ? $request['paymentData']['freightPaymentData']['fees'] : $request['paymentData']['paymentExchangeOrDifferenceData']['fees'];

        $exchangeOrDifferenceCurrentValue -= $change;
        $exchangeOrDifferenceCurrentValue += $fees;

        $returnDTO = CreateReturnDTO::fromRequest($request, $seller?->name, $seller?->email, $exchangeOrDifferenceCurrentValue);

        return $this->repository->create($returnDTO->toArray());
    }

    private function updateReturnExchangeProductMovement(int $returnID, string $status)
    {
        $hasReturnExchangeItems = $this->returnExchangeItemRepository->findByReturnId($returnID);

        if ($hasReturnExchangeItems) {
            $movementData = [
                'status' => $status === 'Ativa' ? 'active' : 'canceled',
                'updated_by' => Auth::user()->id,
                'updated_by_name' => Auth::user()->name,
                'updated_by_email' => Auth::user()->email,
            ];

            $this->productMovementRepository->updateTradeProductsMovement($returnID, $movementData, 'return');
        }

        return true;
    }

    private function updateStockReentryReturnItem(int $returnID, string $status, int $enterpriseID)
    {
        $products = $this->returnItemRepository->findByReturnId($returnID);

        $status = $status === 'Ativa' ? 'active' : 'canceled';

        foreach ($products as $product) {
            $this->stockReentryReturnItemRepository->updateStockReentry(
                $product->product_variant_id,
                $product->quantity,
                $status,
                $enterpriseID,
                $returnID
            );
        }

        return true;
    }

    private function validateExchangeProducts($products)
    {
        foreach ($products as $product) {
            ProductVariantHelper::existsProductVariant($product['product_variant_id']);
            ProductVariantHelper::isProductVariantActive($product['product_variant_id']);
            ProductVariantHelper::hasProductVariantStock($product['product_variant_id']);
            ProductVariantHelper::quantityExceedsStock($product['product_variant_id'], $product['quantity']);
        }
    }

    private function validateReturnProducts($returnData, int $saleID)
    {
        foreach ($returnData as $item) {
            foreach ($item['products'] as $product) {
                ReturnItemHelper::quantityExceedsQuantitySale($product['product_variant_id'], $saleID, $product['returnQuantity']);
            }
        }
    }

    private function validateVinculateReturnProducts($returnData, int $returnID, int $saleID)
    {
        foreach ($returnData as $item) {
            foreach ($item['products'] as $product) {
                ReturnItemHelper::quantityExceedsQuantityExchangeItem($product['product_variant_id'], $saleID, $returnID, $product['returnQuantity']);
            }
        }
    }

    private function createReturnItems(array $returns, int $returnID)
    {
        foreach ($returns as $return) {
            foreach ($return['products'] as $product) {
                $returnItemDTO = CreateReturnItemDTO::fromRequest([
                    'returnID' => $returnID,
                    'productVariantID' => $product['product_variant_id'],
                    'productName' => $product['product_name'],
                    'productSKU' => $product['product_sku'] ?? null,
                    'productCode' => $product['product_code'] ?? null,
                    'productPrice' => $product['product_price'],
                    'productColor' => $product['color'] ?? null,
                    'productColorName' => $product['color_name'] ?? null,
                    'returnQuantity' => $product['returnQuantity'],
                    'total' => $product['returnQuantity'] * $product['product_price'],
                    'reason' => $return['reason'],
                    'description' => $return['description'] ?? null,
                ]);

                $this->returnItemRepository->create($returnItemDTO->toArray());
                $this->createOrUpdateStockReentry($product);
            }
        }

        return true;
    }

    private function createOrUpdateStockReentry($product)
    {

        $existProduct = StockReentryReturnItemHelper::existsProduct($product['product_variant_id']);
        if ($existProduct) {
            $quantityTotal = $existProduct->quantity + $product['returnQuantity'];

            return $this->stockReentryReturnItemRepository->update($existProduct->id, ['quantity' => $quantityTotal]);
        } else {
            $stockReentryDTO = CreateStockReentryReturnItemsDTO::fromRequest($product, Auth::user()->enterprise_id);

            return $this->stockReentryReturnItemRepository->create($stockReentryDTO->toArray());
        }
    }

    private function createExchangeReturnItems(array $exchangeProducts, int $returnID, $hasDelivery)
    {
        if ($exchangeProducts) {
            foreach ($exchangeProducts as $product) {

                $total = $product['offer'] ? $product['offer'] * $product['quantity'] : $product['price'] * $product['quantity'];

                $exchangeReturnItemDTO = CreateExchangeReturnItemDTO::fromRequest([
                    'returnID' => $returnID,
                    'productVariantID' => $product['product_variant_id'],
                    'productName' => $product['name'],
                    'productSKU' => $product['sku'] ?? null,
                    'productCode' => $product['code'] ?? null,
                    'productPrice' => $product['price'],
                    'productColor' => $product['color']['hex_color_code'] ?? null,
                    'productColorName' => $product['color']['name'] ?? null,
                    'quantity' => $product['quantity'],
                    'total' => $total,
                    'productGridSize' => $product['grid_item']['size'] ?? null,
                    'productGridName' => $product['grid_group']['name'] ?? null,
                    'delivered' => $hasDelivery ? 0 : 1,
                    'quantityDelivered' => $hasDelivery ? 0 : $product['quantity'],
                ]);

                $this->returnExchangeItemRepository->create($exchangeReturnItemDTO->toArray());
            }
        }

        return true;
    }

    private function createReturnExchangeProductMovement(int $returnID, int $enterpriseID, string $reason, string $type)
    {
        $returnExchangeProducts = $this->returnExchangeItemRepository->findByReturnId($returnID);

        if ($returnExchangeProducts->isNotEmpty()) {
            foreach ($returnExchangeProducts as $product) {
                $movementData = [
                    'reason' => $reason,
                    'type' => $type,
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
        }

        return true;
    }
}
