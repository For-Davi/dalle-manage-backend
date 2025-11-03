<?php

namespace App\Services;

use App\DTO\Product\Movement\CreateProductMovementDTO;
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
        $movementDTO = CreateProductMovementDTO::fromRequest([
            ...$request->only([
                'reason',
                'type',
                'documentNumber',
                'lotNumber',
                'quantity',
                'unitCost',
                'totalCost',
                'variantID',
                'supplierID',
                'description',
            ]),
            'createdBY' => Auth::user()->id,
            'enterpriseID' => $request->get('enterprise_id'),
        ]);

        $this->repository->create($movementDTO->toArray());

        $this->productVariantRepository->changeStockQuantity(
            $request->variantID,
            $request->type,
            $request->quantity,
        );

        return true;
    }
}
