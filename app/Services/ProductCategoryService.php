<?php

namespace App\Services;

use App\DTO\Product\Category\CreateProductCategoryDTO;
use App\DTO\Product\Category\UpdateProductCategoryDTO;
use App\Helpers\ProductCategoryHelper;
use App\Repositories\ProductCategoryRepository;

class ProductCategoryService
{
    public function __construct(protected ProductCategoryRepository $repository) {}

    public function create($request)
    {
        ProductCategoryHelper::existsCategory(
            $request->name,
            'create'
        );

        $categoryDTO = CreateProductCategoryDTO::fromRequest([
            ...$request->only(['name']),
        ]);

        return $this->repository->create($categoryDTO->toArray());
    }

    public function update($request)
    {
        ProductCategoryHelper::existsCategory(
            $request->name,
            'update',
            $request->id
        );

        $categoryDTO = UpdateProductCategoryDTO::fromRequest([
            ...$request->only(['name']),
        ]);

        return $this->repository->update($request->id, $categoryDTO->toArray());
    }
}
