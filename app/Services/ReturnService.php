<?php

namespace App\Services;

use App\DTO\Return\CreateReturnDTO;
use App\DTO\Return\ReturnItem\CreateReturnItemDTO;
use App\DTO\Return\ExchangeReturnItem\CreateExchangeReturnItemDTO;
use App\DTO\Return\UpdateReturnDTO;
use App\Repositories\ReturnRepository;
use App\Repositories\ReturnItemRepository;
use App\Repositories\ExchangeReturnItemRepository;
use App\Services\ExchangeService;
use App\Services\ClientService;
use App\Helpers\ProductVariantHelper;
use App\Helpers\ReturnHelper;
use App\Helpers\ReturnItemHelper;

class ReturnService
{
    public function __construct(
        protected ReturnRepository $repository,
        protected ReturnItemRepository $returnItemRepository,
        protected ExchangeReturnItemRepository $exchangeReturnItemRepository,
        protected ExchangeService $exchangeService,
        protected ClientService $clientService,
    ) {}

    public function create($request)
    {
        //Verificação dos produtos da devolução
        foreach($request['returnData'] as $item){
            $this->validateReturnProducts($item['products'], $request['saleID']);
        }
        //Verificação dos produtos da vinculação de devolução(caso tenha)
        if($request['returnID']){
            foreach($request['returnData'] as $item){
            $this->validateVinculateReturnProducts($item['products'], $request['returnID']);
        }
        }
        //Verificação dos produtos da troca (caso tenha)
        if($request['exchangeProducts']){
            $this->validateExchangeProducts($request['exchangeProducts']);
        }

        //Criação da devolução
       $returnDTO = CreateReturnDTO::fromRequest($request);
       $return = $this->repository->create($returnDTO->toArray());

        //Criação dos itens da devolução
        $this->createReturnItems($request['returnData'], $return->id);

        //Criação dos itens de troca (caso tenha)
        if($request['exchangeProducts']){
            $this->createExchangeReturnItems($request['exchangeProducts'], $return->id);
        }

        //Se pediu para gerar o crédito, gera crédito, caso o contrário gera estorno
        if($request['exchangeData']['generatesCredit'] === 1 && $request['exchangeData']['exchangeValue'] > 0 && $request['exchangeData']['differenceValue'] === 0){
            return $this->clientService->updateCredit($request['saleID'], $request['exchangeData']['exchangeValue']);
        } else {
            if($request['exchangeData']['exchangeValue'] > 0 || $request['exchangeData']['differenceValue'] > 0){
            $this->exchangeService->create($request['exchangeData'], $request['saleID'], $return->id);
        }
        }

        return true;
        
        //Criação do crédito ao cliente (caso seja informado que é para gerar crédito)
    }

    public function update($request)
    {
        ReturnHelper::existsReturn($request->saleID, $request->id);
        ReturnHelper::isSameStatus($request->id, $request->status);

        $returnDTO = UpdateReturnDTO::fromRequest($request);

        $this->repository->update($request->id, $returnDTO->toArray());

        return $this->exchangeService->updateExchangeAfterReturn($request);
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
        foreach($products as $product){
            ReturnItemHelper::quantityExceedsQuantitySale($product['product_variant_id'], $saleID, $product['returnQuantity']);
        }
    }
    
    private function validateVinculateReturnProducts($products, int $returnID)
    {
        foreach($products as $product){
            ReturnItemHelper::quantityExceedsQuantityExchangeItem($product['product_variant_id'], $returnID, $product['returnQuantity']);
        }
    }

    private function createReturnItems(array $returns, int $returnID)
    {
        foreach($returns as $return){
            foreach($return['products'] as $product){
                $returnItemDTO = CreateReturnItemDTO::fromRequest([
                    'returnID' => $returnID,
                    'productVariantID' => $product['product_variant_id'],
                    'productName' => $product['product_name'],
                    'productSKU' => $product['product_sku'] ?? null,
                    'productPrice' => $product['product_price'],
                    'productColor' => $product['color'] ?? null,
                    'productColorName' => $product['color_name'] ?? null,
                    'returnQuantity' => $product['returnQuantity'],
                    'total' => $product['total'],
                    'reason' => $return['reason'],
                    'description' => $return['description'] ?? null,
                ]);

                $this->returnItemRepository->create($returnItemDTO->toArray());
            }
        }

        return true;
    }

    private function createExchangeReturnItems(array $exchangeProducts, $returnID)
    {
        foreach($exchangeProducts as $product){

            $total = $product['offer'] ? $product['offer']*$product['quantity'] : $product['price']*$product['quantity'];

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

            $this->exchangeReturnItemRepository->create($exchangeReturnItemDTO->toArray());
        }

        return true;
    }
}
