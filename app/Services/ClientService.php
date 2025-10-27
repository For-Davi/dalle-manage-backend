<?php

namespace App\Services;

use App\DTO\Client\CreateClientDTO;
use App\DTO\Client\UpdateClientDTO;
use App\Repositories\ClientRepository;

class ClientService
{
    public function __construct(protected ClientRepository $repository) {}

    public function create($request)
    {
        $clientDTO = CreateClientDTO::fromRequest($request);

        return $this->repository->create($clientDTO->toArray());
    }

    public function update($request)
    {
        $supplierDTO = UpdateClientDTO::fromRequest($request);

        return $this->repository->update($request->id, $supplierDTO->toArray());
    }
}
