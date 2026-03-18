<?php

namespace App\Services;

use App\DTO\SellerRegistration\CreateSellerRegistrationDTO;
use App\Helpers\SellerHelper;
use App\Repositories\SellerRegistrationRepository;

class SellerService
{
    public function __construct(
        protected SellerRegistrationRepository $repository,
    ) {}

    public function create($request)
    {
        SellerHelper::existsEmail($request->email, 'create');
        SellerHelper::existsPhone($request->phone, 'create');

        $sellerDTO = CreateSellerRegistrationDTO::fromRequest($request);

        return $this->repository->create($sellerDTO->toArray());
    }
}
