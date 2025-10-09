<?php

namespace App\Services;

use App\DTO\Enterprise\UpdateEnterpriseDTO;
use App\Repositories\EnterpriseRepository;

class EnterpriseService
{
    public function __construct(
        protected EnterpriseRepository $repository,
    ) {}

    public function update($request)
    {
        $enterpriseDTO = UpdateEnterpriseDTO::fromRequest([
            ...$request->only([
                'name',
                'email',
                'phone',
                'cpf',
                'cnpj',
                'cep',
                'state',
                'city',
                'neighborhood',
                'address',
                'numberAddress',
                'complement',
            ]),
        ]);

        return $this->repository->update($request->id, $enterpriseDTO->toArray());
    }
}
