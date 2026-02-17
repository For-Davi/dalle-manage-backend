<?php

namespace App\Services;

use App\DTO\Client\CreateClientDTO;
use App\DTO\Client\UpdateClientDTO;
use App\Repositories\ClientRepository;
use App\Repositories\SaleRepository;

class ClientService
{
    public function __construct(
        protected ClientRepository $repository,
        protected SaleRepository $saleRepository,
    ) {}

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

    public function updateCredit($saleID = null, $credit, $clientID = null)
    {
        if(!$clientID){
            $sale = $this->saleRepository->findById($saleID);

            return $this->repository->update($sale->client_id, ['credits' => $credit]);
        }

        return $this->repository->update($clientID, ['credits' => $credit]);
    }
}
