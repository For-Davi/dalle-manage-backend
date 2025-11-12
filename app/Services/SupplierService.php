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
        $supplierDTO = CreateSupplierDTO::fromRequest([
            ...$request->only([
                'name',
                'email',
                'cpf',
                'cnpj',
                'stateRegistration',
                'municipalRegistration',
                'phone',
                'site',
                'country',
                'state',
                'city',
                'cep',
                'neighborhood',
                'address',
                'number',
                'categorySupplierId',
                'description',
                'complement',
            ]),
        ]);

        return $this->repository->create($supplierDTO->toArray());
    }

    public function update($request)
    {
        $supplierDTO = UpdateSupplierDTO::fromRequest([
            ...$request->only([
                'name',
                'email',
                'cpf',
                'cnpj',
                'stateRegistration',
                'municipalRegistration',
                'phone',
                'site',
                'country',
                'state',
                'city',
                'cep',
                'neighborhood',
                'address',
                'number',
                'categorySupplierId',
                'description',
                'active',
                'complement',
            ]),
        ]);

        return $this->repository->update($request->id, $supplierDTO->toArray());
    }
}
