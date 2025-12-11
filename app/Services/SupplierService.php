<?php

namespace App\Services;

use App\DTO\Supplier\CreateSupplierDTO;
use App\DTO\Supplier\UpdateSupplierDTO;
use App\Repositories\SupplierRepository;

class SupplierService
{
    public function __construct(protected SupplierRepository $repository) {}

    public function create($request)
    {
        $supplierDTO = CreateSupplierDTO::fromRequest($request);

        return $this->repository->create($supplierDTO->toArray());
    }

    public function update($request)
    {
        $supplierDTO = UpdateSupplierDTO::fromRequest($request);

        return $this->repository->update($request->id, $supplierDTO->toArray());
    }
}
