<?php

namespace App\Services;

use App\DTO\Product\Color\CreateProductColorDTO;
use App\DTO\Product\Color\UpdateProductColorDTO;
use App\Helpers\ProductColorHelper;
use App\Repositories\ProductColorRepository;

class ProductColorService
{
    public function __construct(protected ProductColorRepository $repository) {}

    public function create($request)
    {
        ProductColorHelper::existsColor(
            $request->name,
            'create'
        );

        $productColorDTO = CreateProductColorDTO::fromRequest($request);

        return $this->repository->create($productColorDTO->toArray());
    }

    public function update($request)
    {
        ProductColorHelper::existsColor(
            $request->name,
            'update',
            $request->id
        );

        $productColorDTO = UpdateProductColorDTO::fromRequest($request);

        return $this->repository->update($request->id, $productColorDTO->toArray());
    }
}
