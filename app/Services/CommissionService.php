<?php

namespace App\Services;

use App\DTO\Commission\CreateCommissionDTO;
use App\Repositories\ClientRepository;
use App\Repositories\CommissionRepository;
use App\Repositories\EmployeeRepository;
use App\Repositories\ProductAdvancedRepository;
use App\Repositories\ProductRepository;
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
    ) {}

    public function create(int $saleID, int $sellerID, array $products, string $type)
    {
        $seller = $this->employeeRepository->findById($sellerID);

        // Retorna um array com os preço dos produtos ja calculados
        $calculatedValueProducts = $this->calculatedProducts($products);

        // Retorna um array com o preço do produto calculado pela comissão
        $commissionProducts = $this->calculateCommission($calculatedValueProducts);

        // Caso a comissão seja feita por uma venda ele cria uma comissão com esse tipo Sale
        if ($type === 'sale') {
            return $this->createCommissionSale($commissionProducts, $saleID, $seller);
        }

        // Caso a comissão seja feita por uma devolução ele cria uma comissão com esse tipo Return
        if ($type === 'return') {
            return $this->createCommissionsReturn($commissionProducts, $saleID, $seller);
        }
    }

    private function createCommissionSale(array $commissionProducts, int $saleID, $seller)
    {
        $sale = $this->saleRepository->findById($saleID);
        $totalCommission = 0;

        foreach ($commissionProducts as $item) {
            $commissionDTO = CreateCommissionDTO::fromRequest([
                'sale_id' => $saleID,
                'type' => 'sale',
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
        $currentTotal = $sale->current_total - $totalCommission;
        $this->saleRepository->update($saleID, ['current_total' => $currentTotal]);

        return true;
    }

    private function createCommissionsReturn(array $commissionProducts, int $saleID, $seller)
    {

        // Lógica da comissão
        foreach ($commissionProducts as $item) {
            $commissionDTO = CreateCommissionDTO::fromRequest([
                'sale_id' => $saleID,
                'type' => 'sale',
                'status' => 'active',
                'product_id' => $item['product_id'],
                'product_name' => $item['product_name'],
                'seller_id' => $seller->id,
                'seller_name' => $seller->name,
                'seller_email' => $seller->email,
                'percentage' => $item['percentage'],
                'commission_value' => $item['commission_value'],
            ]);

            $this->commissionRepository->create($commissionDTO->toArray());
        }

        return true;
    }

    private function calculatedProducts(array $products)
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
            } else {
                continue;
            }
        }

        return $commissionProducts;
    }
}
