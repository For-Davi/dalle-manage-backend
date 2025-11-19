<?php

namespace App\Services;

use App\DTO\Supplier\Catalog\CreateSupplierCatalogDTO;
use App\DTO\Supplier\Catalog\UpdateSupplierCatalogDTO;
use App\Helpers\SupplierCatalogHelper;
use App\Repositories\SupplierCatalogRepository;

class SupplierCatalogService
{
    public function __construct(protected SupplierCatalogRepository $repository) {}

    public function create($request)
    {
        SupplierCatalogHelper::existsBond($request->productVariantID, $request->supplierID);

        $catalogDTO = CreateSupplierCatalogDTO::fromRequest([
            ...$request->only(['productVariantID', 'price', 'supplierID', 'description']),
        ]);

        return $this->repository->create($catalogDTO->toArray());
    }

    public function update($request)
    {
        $catalogDTO = UpdateSupplierCatalogDTO::fromRequest([
            ...$request->only(['productVariantID', 'price', 'supplierID', 'description']),
        ]);

        return $this->repository->updateBySupplierAndVariant(
            $catalogDTO->supplier_id,
            $catalogDTO->product_variant_id,
            $catalogDTO->toArray()
        );
    }
}
