<?php

namespace App\Services;

use App\DTO\DeliveryGuy\CreateOrUpdateDeliveryGuyDTO;
use App\Repositories\DeliveryGuyRepository;

class DeliveryGuyService
{
    public function __construct(
        protected DeliveryGuyRepository $repository,
    ) {}

    public function create($request)
    {
        $deliveryGuyDTO = CreateOrUpdateDeliveryGuyDTO::fromRequest($request);

        return $this->repository->create($deliveryGuyDTO->toArray());
    }

    public function update($request)
    {
        $deliveryGuyDTO = CreateOrUpdateDeliveryGuyDTO::fromRequest($request);

        return $this->repository->update($request->deliveryGuyID, $deliveryGuyDTO->toArray());
    }
}
