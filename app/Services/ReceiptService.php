<?php

namespace App\Services;

use App\DTO\Receipt\CreateReceiptDTO;
use App\DTO\Receipt\UpdateReceiptDTO;
use App\Repositories\ReceiptRepository;

class ReceiptService
{
    public function __construct(protected ReceiptRepository $repository) {}

    public function create($request)
    {
        $receiptDTO = CreateReceiptDTO::fromRequest($request);

        return $this->repository->create($receiptDTO->toArray());
    }

    public function update($request)
    {
        $receiptDTO = UpdateReceiptDTO::fromRequest($request);

        return $this->repository->update($request->id, $receiptDTO->toArray());
    }
}
