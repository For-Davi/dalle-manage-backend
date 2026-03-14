<?php

namespace App\Services;

use App\DTO\Commission\CreateCommissionDTO;
use App\Repositories\ClientRepository;
use App\Repositories\CommissionRepository;
use App\Repositories\EmployeeRepository;
use App\Repositories\ProductAdvancedRepository;
use App\Repositories\ProductRepository;
use App\Repositories\ProductVariantRepository;
use App\Repositories\SaleRepository;

class CommissionService
{
    public function __construct(
        protected ClientRepository $repository,
        protected ProductRepository $productRepository,
        protected ProductAdvancedRepository $productAdavancedRepository,
        protected EmployeeRepository $employeeRepository,
        protected CommissionRepository $commissionRepository,
        protected SaleRepository $saleRepository,
        protected ProductVariantRepository $productVariantRepository,
    ) {}

    public function create(int $saleID, int $sellerID, ?int $returnID, array $products)
    {
        $seller = $this->employeeRepository->findById($sellerID);

        // Retorna um array com os preço dos produtos ja calculados
        $calculatedValueProducts = $returnID ? $this->calculatedReturnExchangeProducts($products) : $this->calculatedSaleProducts($products);

        // Retorna um array com o preço do produto calculado pela comissão
        $commissionProducts = $this->calculateCommission($calculatedValueProducts);

        // Caso a comissão seja feita por uma venda ele cria uma comissão com o tipo Sale
        if (! $returnID && count($commissionProducts) > 0) {
            return $this->createCommission($commissionProducts, $saleID, null, $seller, 'sale');
        }

        // Caso a comissão seja feita por uma devolução ele cria uma comissão com o tipo Return
        if ($returnID && count($commissionProducts) > 0) {
            return $this->createCommission($commissionProducts, $saleID, $returnID, $seller, 'return');
        }

        return true;
    }

    private function createCommission(array $commissionProducts, int $saleID, ?int $returnID, $seller, string $type)
    {
        $sale = $this->saleRepository->findById($saleID);
        $totalCommission = 0;

        foreach ($commissionProducts as $item) {
            $commissionDTO = CreateCommissionDTO::fromRequest([
                'sale_id' => $saleID,
                'return_id' => $returnID,
                'type' => $type,
                'status' => 'active',
                'product_id' => $item['product_id'],
                'product_name' => $item['product_name'],
                'seller_id' => $seller->id,
                'seller_name' => $seller->name,
                'seller_email' => $seller->email,
                'percentage' => $item['percentage'],
                'commission_value' => $item['commission_value'],
            ]);

            $totalCommission += $item['commission_value'];

            $this->commissionRepository->create($commissionDTO->toArray());
        }

        if ($type !== 'return') {
            $currentTotal = $sale->current_total - $totalCommission;
            $this->saleRepository->update($saleID, ['current_total' => $currentTotal]);
        }

        return true;
    }

    private function calculatedSaleProducts(array $products)
    {
        $calculatedValueProducts = [];

        foreach ($products as $product) {
            $productID = $product['productID'];

            $value = $product['offer'] && $product['offer'] > 0 ? $product['offer'] * $product['newQuantity'] : $product['price'] * $product['newQuantity'];

            if (isset($calculatedValueProducts[$productID])) {
                $calculatedValueProducts[$productID]['value'] += $value;
            } else {
                $calculatedValueProducts[$productID] = [
                    'product_id' => $productID,
                    'value' => $value,
                ];
            }
        }

        return $calculatedValueProducts;
    }

    private function calculatedReturnExchangeProducts(array $products)
    {
        $calculatedValueProducts = [];

        foreach ($products as $product) {
            $productVariant = $this->productVariantRepository->findById($product['product_variant_id']);

            if (isset($calculatedValueProducts[$productVariant->product_id])) {
                $calculatedValueProducts[$productVariant->product_id]['value'] += $product['total'];
            } else {
                $calculatedValueProducts[$productVariant->product_id] = [
                    'product_id' => $productVariant->product_id,
                    'value' => $product['total'],
                ];
            }
        }

        return $calculatedValueProducts;
    }

    private function calculateCommission($calculatedValueProducts)
    {
        $commissionProducts = [];

        foreach ($calculatedValueProducts as $item) {
            $product = $this->productRepository->findById($item['product_id']);
            $productAdvanced = $this->productAdavancedRepository->findByProduct($item['product_id']);

            if ($productAdvanced->has_commission && $productAdvanced->commission_percentage > 0) {
                $commissionProducts[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'percentage' => $productAdvanced->commission_percentage,
                    'commission_value' => round(($productAdvanced->commission_percentage / 100) * $item['value'], 2),
                ];
            }
        }

        return $commissionProducts;
    }
}
