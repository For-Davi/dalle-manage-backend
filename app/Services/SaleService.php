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
use App\Repositories\ProductVariantRepository;
use App\Repositories\SaleDeliveryRepository;
use App\Repositories\SaleItemRepository;
use App\Repositories\SalePaymentsMethodRepository;
use App\Repositories\SaleRepository;
use App\Repositories\UserRepository;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

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
        protected ProductMovementService $productMovementService,
    ) {}

    public function create($request)
    {
        $enterpriseID = $request->get('enterprise_id');

        $this->validateSale($request->input('saleData.products'));

        $totalValue = $this->getTotalValue([
            $request->input('saleData.totalPrice'),
            $request->input('deliveryData.freightValue'),
            $request->input('paymentData.fees'),
        ]);

        $sale = $this->createSale($request, $enterpriseID, $totalValue);

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

        return 'Caso o e-mail informado exista, o cupom será enviado.';
    }

    // Função da criação de pagamentos
    private function createSalePaymentsMethods(array $payments, int $saleID, int $enterpriseID)
    {

        foreach ($payments['payment'] as $payment) {
            // Validação do tipo de pagamento
            SaleHelper::findReceipt($payment['receiptID']);
            $paymentMethodID = SaleHelper::findPaymentMethodID($payment['paymentType'], $enterpriseID, $payment['receiptID']);

            // Caso haja a quantidade de parcelas e o valor das parcelas ele salva, caso não haja ele coloca como nulo
            if (($payment['installment']['value'] !== null && $payment['installment']['value'] >= 1 && $payment['installment']['value'] <= 12) && ($payment['installment']['amount'] !== null && $payment['installment']['amount'] !== '' && $payment['installment']['amount'] > 0)) {
                $installmentValue = $payment['installment']['value'];

                $installmentAmount = $payment['installment']['amount'];
            } else {
                $installmentValue = null;

                $installmentAmount = null;
            }

            $salePaymentDTO = CreateSalePaymentDTO::fromRequest([
                'saleID' => $saleID,
                'paymentMethodID' => $paymentMethodID,
                'receiptID' => $payment['receiptID'],
                'installments' => $installmentValue,
                'value' => $installmentAmount ? $installmentAmount : $payment['value'],
            ]);

            $this->salePaymentsRepository->create($salePaymentDTO->toArray());
        }
    }

    // Função da criação de entrega
    private function createSaleDelivery($deliveryData, int $saleID)
    {

        $deliveryDTO = CreateSaleDeliveriesDTO::fromRequest($deliveryData, $saleID);

        $this->saleDeliveryRepository->create($deliveryDTO->toArray());
    }

    // Função da crição dos itens de venda
    private function createSaleItens(array $products, int $saleID, int $enterpriseID)
    {
        foreach ($products['products'] as $product) {
            $productVariant = $this->productVariantRepository->findById($product['productVariantID']);

            $productVariant->load(['product']);

            $total = $product['offer'] !== null && $product['offer'] > 0 ? $product['offer'] * $product['newQuantity'] : $product['price'] * $product['newQuantity'];

            $saleItemDTO = CreateSaleItemDTO::fromRequest([
                'saleID' => $saleID,
                'productVariantID' => $productVariant->id,
                'productName' => $productVariant->product->name,
                'productSKU' => $productVariant->sku ?? null,
                'productPrice' => $product['offer'] > 0 ? $product['offer'] : $product['price'],
                'quantity' => $product['newQuantity'],
                'total' => $total,
            ]);

            $productMovement = new Request([
                'reason' => 'sale',
                'type' => 'out',
                'documentNumber' => null,
                'lotNumber' => null,
                'quantity' => $product['newQuantity'],
                'unitCost' => null,
                'totalCost' => null,
                'variantID' => $productVariant->id,
                'supplierID' => null,
                'description' => null,
                'enterprise_id' => $enterpriseID,
            ]);

            $this->productMovementService->create($productMovement);
            $this->saleItemRepository->create($saleItemDTO->toArray());
        }
    }

    // Validação dos itens de venda
    private function validateSale($products)
    {
        foreach ($products as $product) {
            ProductVariantHelper::existsProductVariant($product['productVariantID']);
            ProductVariantHelper::isProductVariantActive($product['productVariantID']);
            ProductVariantHelper::hasProductVariantStock($product['productVariantID']);
            ProductVariantHelper::quantityExceedsStock($product['productVariantID'], $product['newQuantity']);
        }
    }

    // Soma do total da venda
    private function getTotalValue($values)
    {
        $total = 0;
        foreach ($values as $value) {
            $total += $value;
        }

        return $total;
    }

    // Criação da venda
    private function createSale($request, int $enterpriseID, float $totalValue)
    {

        $date = now()->format('d-m-Y H:i:s');

        $saleDTO = CreateSaleDTO::fromRequest([
            'enterpriseID' => $enterpriseID,
            'sellerID' => $request->sellerID,
            'clientID' => $request->clientData ? $request->input('clientData.id') : null,
            'fees' => $request->input('paymentData.fees'),
            'totalValue' => $totalValue,
            'change' => $request->input('paymentData.change'),
            'date' => $date,
        ]);

        return $this->saleRepository->create($saleDTO->toArray());
    }

    private function updateClientData($clientData)
    {
        $clientDTO = UpdateClientDTO::fromRequest([
            'name' => $clientData['name'],
            'email' => $clientData['email'],
            'sex' => $clientData['sex'],
            'phone' => $clientData['phone'],
            'cpf' => $clientData['cpf'],
            'cnpj' => $clientData['cnpj'],
            'stateRegistration' => $clientData['stateRegistration'],
            'municipalRegistration' => $clientData['municipalRegistration'],
            'dateBirthday' => $clientData['dateBirthday'],
            'cep' => $clientData['cep'],
            'country' => $clientData['country'],
            'state' => $clientData['state'],
            'city' => $clientData['city'],
            'neighborhood' => $clientData['neighborhood'],
            'address' => $clientData['address'],
            'number' => $clientData['number'],
            'complement' => $clientData['complement'],
            'description' => $clientData['description'],
        ]);

        $this->clientRepository->update($clientData['id'], $clientDTO->toArray());
    }
}
