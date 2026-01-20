<?php

namespace App\Services;

use App\DTO\Client\UpdateClientDTO;
use App\DTO\Sale\CreateSaleDTO;
use App\DTO\Sale\SaleDelivery\CreateSaleDeliveriesDTO;
use App\DTO\Sale\SaleItem\CreateSaleItemDTO;
use App\DTO\Sale\SalePayment\CreateSalePaymentDTO;
use App\Helpers\ProductVariantHelper;
use App\Helpers\SaleHelper;
use App\Jobs\SendCouponToEmailJob;
use App\Repositories\ClientRepository;
use App\Repositories\EmployeeRepository;
use App\Repositories\ProductVariantRepository;
use App\Repositories\ReceiptRepository;
use App\Repositories\SaleDeliveryRepository;
use App\Repositories\SaleItemRepository;
use App\Repositories\SalePaymentsMethodRepository;
use App\Repositories\SaleRepository;
use App\Repositories\UserRepository;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SaleService
{
    public function __construct(
        protected SaleRepository $saleRepository,
        protected ProductVariantRepository $productVariantRepository,
        protected SalePaymentsMethodRepository $salePaymentsRepository,
        protected SaleDeliveryRepository $saleDeliveryRepository,
        protected SaleItemRepository $saleItemRepository,
        protected ClientRepository $clientRepository,
        protected UserRepository $userRepository,
        protected EmployeeRepository $employeeRepository,
        protected ProductMovementService $productMovementService,
        protected ReceiptRepository $receiptRepository,
        protected CommissionService $commissionService,
    ) {}

    public function getSales()
    {
        $sales = $this->saleRepository->getAllByEnterprise();

        return $sales;
    }

    public function create($request)
    {
        $enterpriseID = Auth::user()->enterprise_id;

        $this->validateSale($request->input('saleData.products'));

        // Cria a venda
        $sale = $this->createSale($request, $enterpriseID);

        // Criação do metódo de pagamentos
        $this->createSalePaymentsMethods($request->paymentData, $sale->id, $enterpriseID);

        // Criação do itens da venda
        $this->createSaleItens($request->saleData, $sale->id, $enterpriseID);

        // Criação do frete
        if ($request->input('deliveryData.freight')) {
            $this->createSaleDelivery($request->deliveryData, $sale->id);
        }

        // Atualização dos dados do cliente
        if ($request->clientData) {
            $this->updateClientData($request->clientData);
        }

        // Cria comissão
        if($sale->seller_id){
            $this->commissionService->create($sale->id, $sale->seller_id, $request->saleData['products'], 'sale');
        }

        return $sale;
    }

    public function export($request)
    {
        $dateTime = now()->format('Ymd_His');
        $couponData = $this->saleRepository->getCouponInfos($request->saleID);

        if ($couponData) {
            $fileName = "cupom_fiscal_{$dateTime}.pdf";

            $pdf = Pdf::loadView('exports.tax-coupon-pdf', [
                'couponData' => $couponData,
            ]);

            return $pdf->download($fileName);
        }
    }

    public function sendToEmail($request)
    {
        if ($request->email) {

            $couponData = $this->saleRepository->getCouponInfos($request->saleID);

            SendCouponToEmailJob::dispatch($request->email, $couponData);
        }

        return 'O cupom será enviado ao e-mail informado';
    }

    private function createSalePaymentsMethods(array $payments, int $saleID, int $enterpriseID): void
    {
        foreach ($payments['payment'] as $payment) {
            SaleHelper::existsReceipt($payment['receiptID']);

            $paymentMethodID = SaleHelper::findPaymentMethodID(
                $payment['paymentType'],
                $enterpriseID,
                $payment['receiptID']
            );

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
                'paymentMethodID' => $paymentMethodID,
                'receiptID' => $payment['receiptID'],
                'receiptName' => $receipt->identifier,
                'installments' => $installments,
                'value' => $amount,
            ]);

            $this->salePaymentsRepository->create($salePaymentDTO->toArray());
        }
    }

    private function createSaleDelivery($deliveryData, int $saleID)
    {
        $deliveryDTO = CreateSaleDeliveriesDTO::fromRequest($deliveryData, $saleID);
        $this->saleDeliveryRepository->create($deliveryDTO->toArray());
    }

    private function createSaleItens(array $products, int $saleID, int $enterpriseID): void
    {
        foreach ($products['products'] as $product) {
            $productVariant = $this->productVariantRepository
                ->findById($product['productVariantID'])
                ->loadMissing(['product.category', 'color', 'gridItem.gridGroup', 'suppliers']);

            $price = $product['offer'] ?? $product['price'];
            $hasOffer = $price > 0 && isset($product['offer']);
            $unitPrice = $hasOffer ? $product['offer'] : $product['price'];
            $quantity = $product['newQuantity'] ?? 0;

            $total = $unitPrice * $quantity;

            $saleItemDTO = CreateSaleItemDTO::fromRequest([
                'saleID' => $saleID,
                'productVariantID' => $productVariant->id,
                'productName' => $productVariant->product->name,
                'productSKU' => $productVariant->sku ?? null,
                'productPrice' => $unitPrice,
                'productColor' => $productVariant->color?->hex_color_code ?? null,
                'productColorName' => $productVariant->color?->name ?? null,
                'productGridSize' => $productVariant->gridItem?->size ?? null,
                'productGridName' => $productVariant->gridItem?->gridGroup?->name ?? null,
                'productCode' => $productVariant->code,
                'productCategory' => $productVariant->product->category?->name ?? null,
                'quantity' => $quantity,
                'total' => $total,
            ]);

            $movementData = [
                'reason' => 'sale',
                'type' => 'out',
                'documentNumber' => null,
                'lotNumber' => null,
                'quantity' => $quantity,
                'unitCost' => null,
                'totalCost' => null,
                'variantID' => $productVariant->id,
                'supplierID' => null,
                'description' => null,
                'enterprise_id' => $enterpriseID,
            ];

            $this->productMovementService->create(new Request($movementData));
            $this->saleItemRepository->create($saleItemDTO->toArray());
        }
    }

    private function validateSale($products)
    {
        foreach ($products as $product) {
            ProductVariantHelper::existsProductVariant($product['productVariantID']);
            ProductVariantHelper::isProductVariantActive($product['productVariantID']);
            ProductVariantHelper::hasProductVariantStock($product['productVariantID']);
            ProductVariantHelper::quantityExceedsStock($product['productVariantID'], $product['newQuantity']);
        }
    }

    private function getTotalValue($values)
    {
        $total = 0;
        foreach ($values as $value) {
            $total += $value;
        }

        return $total;
    }

    private function createSale($request, $enterpriseID)
    {

        $totalValue = $this->getTotalValue([
            $request->input('saleData.totalPrice'),
            $request->input('deliveryData.freightValue'),
            $request->input('paymentData.fees'),
        ]);

        if ($request->sellerID) {
            $seller = $this->employeeRepository->findById($request->sellerID);
        }

        $saleDTO = CreateSaleDTO::fromRequest([
            'enterpriseID' => $enterpriseID,
            'sellerID' => $request->sellerID,
            'sellerName' => $seller->name ?? null,
            'clientID' => $request->clientData['id'] ?? null,
            'clientName' => $request->clientData['name'] ?? null,
            'fees' => $request->input('paymentData.fees'),
            'totalValue' => $totalValue,
            'change' => $request->input('paymentData.change'),
        ]);

        return $this->saleRepository->create($saleDTO->toArray());
    }

    private function updateClientData($clientData)
    {
        $clientDTO = UpdateClientDTO::fromRequest($clientData);

        $this->clientRepository->update($clientData['id'], $clientDTO->toArray());
    }
}
