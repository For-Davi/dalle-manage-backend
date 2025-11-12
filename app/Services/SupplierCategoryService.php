<?php

namespace App\Services;

use App\DTO\Supplier\Category\CreateSupplierCategoryDTO;
use App\DTO\Supplier\Category\UpdateSupplierCategoryDTO;
use App\Helpers\SupplierCategoryHelper;
use App\Repositories\SupplierCategoryRepository;

class SupplierCategoryService
{
    public function __construct(protected SupplierCategoryRepository $repository) {}

    public function create($request)
    {
        SupplierCategoryHelper::existsCategory(
            $request->name,
            'create'
        );

        $categoryDTO = CreateSupplierCategoryDTO::fromRequest([
            ...$request->only(['name']),
        ]);

        return $this->repository->create($categoryDTO->toArray());
    }

    public function update($request)
    {
        SupplierCategoryHelper::existsCategory(
            $request->name,
            'update',
            $request->id
        );

        $categoryDTO = UpdateSupplierCategoryDTO::fromRequest([
            ...$request->only(['name']),
        ]);

        return $this->repository->update($request->id, $categoryDTO->toArray());
    }
}
