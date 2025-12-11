<?php

namespace App\Services;

use App\DTO\Product\Movement\CreateProductMovementDTO;
use App\Repositories\ProductMovementRepository;
use App\Repositories\ProductVariantRepository;

class ProductMovementService
{
    public function __construct(
        private ProductMovementRepository $repository,
        private ProductVariantRepository $productVariantRepository
    ) {}

    public function create($request)
    {
        $movementDTO = CreateProductMovementDTO::fromRequest($request);

        $this->repository->create($movementDTO->toArray());

        $this->productVariantRepository->changeStockQuantity(
            $request->variantID,
            $request->type,
            $request->quantity,
        );

        return true;
    }
}
