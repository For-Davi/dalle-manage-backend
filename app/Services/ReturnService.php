<?php

namespace App\Services;

use App\DTO\Return\CreateReturnDTO;
use App\DTO\Return\ExchangeReturnItem\CreateExchangeReturnItemDTO;
use App\DTO\Return\ReturnItem\CreateReturnItemDTO;
use App\DTO\Return\UpdateReturnDTO;
use App\Helpers\ProductVariantHelper;
use App\Helpers\ReturnHelper;
use App\Helpers\ReturnItemHelper;
use App\Repositories\ReturnExchangeItemRepository;
use App\Repositories\ReturnItemRepository;
use App\Repositories\ReturnRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReturnService
{
    public function __construct(
        protected ReturnRepository $repository,
        protected ReturnItemRepository $returnItemRepository,
        protected ReturnExchangeItemRepository $returnExchangeItemRepository,
        protected ExchangeService $exchangeService,
        protected ClientService $clientService,
        protected ProductMovementService $productMovementService,
    ) {}

    public function create($request)
    {
        $enterpriseID = Auth::user()->enterprise_id;

        // Verificação dos produtos da devolução
        foreach ($request['returnData'] as $item) {
            $this->validateReturnProducts($item['products'], $request['saleID']);
        }
        // Verificação dos produtos da vinculação de devolução(caso tenha)
        if ($request['returnID']) {
            foreach ($request['returnData'] as $item) {
                $this->validateVinculateReturnProducts($item['products'], $request['returnID']);
            }
        }
        // Verificação dos produtos da troca (caso tenha)
        $this->validateExchangeProducts($request['exchangeProducts']);

        // Criação da devolução
        $returnDTO = CreateReturnDTO::fromRequest($request);
        $return = $this->repository->create($returnDTO->toArray());

        // Criação dos itens da devolução
        $this->createReturnItems($request['returnData'], $return->id);

        // Criação dos itens de troca (caso tenha)
        if ($request['exchangeProducts']) {
            $this->createExchangeReturnItems($request['exchangeProducts'], $return->id, $enterpriseID);
        }

        // Validação se gera crédito ou estorno/diferença
        $createCredit = $request['exchangeData']['generatesCredit'] === 1 && $request['exchangeData']['exchangeValue'] > 0 && $request['exchangeData']['differenceValue'] === 0;
        $createExchangeOrDifference = $request['exchangeData']['exchangeValue'] > 0 || $request['exchangeData']['differenceValue'] > 0;

        // Gera crédito ao cliente
        if ($createCredit) {
            return $this->clientService->updateCredit($request['saleID'], $request['exchangeData']['exchangeValue']);
        }

        // Cria estorno/diferença, caso nao crie, é feita a retirada do estoque dos produtos trocados (caso tenha)
        if ($createExchangeOrDifference) {
            return $this->exchangeService->create($request['exchangeData'], $request['saleID'], $return->id);
        } else {
            return $this->updateProductMovement($return->id, $enterpriseID, 'trade', 'out');
        }
    }

    public function update($request)
    {
        $enterpriseID = Auth::user()->enterprise_id;

        ReturnHelper::existsReturn($request->saleID, $request->id);
        ReturnHelper::isSameStatus($request->id, $request->status);

        $returnDTO = UpdateReturnDTO::fromRequest($request);

        $return = $this->repository->update($request->id, $returnDTO->toArray());

        $this->exchangeService->updateExchangeAfterReturn($request);

        return $this->validateAndUpdateProductMovement($return, $enterpriseID);
    }

    private function validateAndUpdateProductMovement($return, int $enterpriseID)
    {
        if ($return->status === 'active') {
            return $this->updateProductMovement($return->id, $enterpriseID, 'trade', 'out');
        }
        if ($return->status === 'canceled') {
            return $this->updateProductMovement($return->id, $enterpriseID, 'return_canceled', 'in');
        }
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

    private function validateReturnProducts($products, int $saleID)
    {
        foreach ($products as $product) {
            ReturnItemHelper::quantityExceedsQuantitySale($product['product_variant_id'], $saleID, $product['returnQuantity']);
        }
    }

    private function validateVinculateReturnProducts($products, int $returnID)
    {
        foreach ($products as $product) {
            ReturnItemHelper::quantityExceedsQuantityExchangeItem($product['product_variant_id'], $returnID, $product['returnQuantity']);
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
                    'productPrice' => $product['product_price'],
                    'productColor' => $product['color'] ?? null,
                    'productColorName' => $product['color_name'] ?? null,
                    'returnQuantity' => $product['returnQuantity'],
                    'total' => $product['returnQuantity'] * $product['product_price'],
                    'reason' => $return['reason'],
                    'description' => $return['description'] ?? null,
                ]);

                $this->returnItemRepository->create($returnItemDTO->toArray());
            }
        }

        return true;
    }

    private function createExchangeReturnItems(array $exchangeProducts, int $returnID, int $enterpriseID)
    {
        if ($exchangeProducts) {
            foreach ($exchangeProducts as $product) {

                $total = $product['offer'] ? $product['offer'] * $product['quantity'] : $product['price'] * $product['quantity'];

                $exchangeReturnItemDTO = CreateExchangeReturnItemDTO::fromRequest([
                    'returnID' => $returnID,
                    'productVariantID' => $product['product_variant_id'],
                    'productName' => $product['name'],
                    'productSKU' => $product['sku'] ?? null,
                    'productPrice' => $product['price'],
                    'productColor' => $product['color']['hex_color_code'] ?? null,
                    'productColorName' => $product['color']['name'] ?? null,
                    'quantity' => $product['quantity'],
                    'total' => $total,
                ]);

                $this->returnExchangeItemRepository->create($exchangeReturnItemDTO->toArray());
            }
        }

        return true;
    }

    private function updateProductMovement(int $returnID, int $enterpriseID, string $reason, string $type)
    {
        $returnExchangeProducts = $this->returnExchangeItemRepository->findByReturnId($returnID);

        \Log::info(['dados cehgaram']);

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
