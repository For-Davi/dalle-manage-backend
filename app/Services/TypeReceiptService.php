<?php

namespace App\Services;

use App\DTO\Receipt\Type\CreateTypeReceiptDTO;
use App\DTO\Receipt\Type\UpdateTypeReceiptDTO;
use App\Helpers\TypeReceiptHelper;
use App\Repositories\TypeReceiptRepository;

class TypeReceiptService
{
    public function __construct(protected TypeReceiptRepository $repository) {}

    public function create($request)
    {
        TypeReceiptHelper::existsType(
            $request->name,
            'create'
        );

        $typesDTO = CreateTypeReceiptDTO::fromRequest([
            ...$request->only(['name']),
        ]);

        return $this->repository->create($typesDTO->toArray());
    }

    public function update($request)
    {
        TypeReceiptHelper::existsType(
            $request->name,
            'update',
            $request->id
        );

        $typesDTO = UpdateTypeReceiptDTO::fromRequest([
            ...$request->only(['name']),
        ]);

        return $this->repository->update($request->id, $typesDTO->toArray());
    }
}
