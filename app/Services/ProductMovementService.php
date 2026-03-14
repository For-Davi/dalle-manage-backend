<?php

namespace App\Services;

use App\DTO\Product\Movement\CreateProductMovementDTO;
use App\DTO\Product\Movement\UpdateProductMovementDTO;
use App\Helpers\ProductMovementHelper;
use App\Repositories\ProductMovementRepository;
use App\Repositories\ProductVariantRepository;
use Illuminate\Support\Facades\Auth;

class ProductMovementService
{
    public function __construct(
        private ProductMovementRepository $repository,
        private ProductVariantRepository $productVariantRepository
    ) {}

    public function create($request)
    {
        $movementDTO = CreateProductMovementDTO::fromRequest($request, $request['returnID'], $request['saleID']);

        $this->repository->create($movementDTO->toArray());

        $this->productVariantRepository->changeStockQuantity(
            $request->variantID,
            $request->type,
            $request->quantity,
        );

        return true;
    }

    public function update($request)
    {
        $movementDTO = UpdateProductMovementDTO::fromRequest($request);

        if ($request['returnID']) {
            return $this->repository->updateTradeProductsMovement($request['returnID'], $movementDTO->toArray(), 'return');
        }

        return $this->repository->updateTradeProductsMovement($request['saleID'], $movementDTO->toArray(), 'sale');
    }

    public function createWhenItsStockReentry($request)
    {
        ProductMovementHelper::validateStockReentryQuantity($request['quantity'], $request['variantID'], Auth::user()->enterprise_id);

        $movementDTO = CreateProductMovementDTO::fromRequest($request);

        $this->repository->create($movementDTO->toArray());

        $this->productVariantRepository->changeStockReentryReturnItemQuantity(
            $request->variantID,
            $request->type,
            $request->quantity,
        );

        return true;
    }
}
