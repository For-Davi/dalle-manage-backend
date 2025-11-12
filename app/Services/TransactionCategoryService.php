<?php

namespace App\Services;

use App\DTO\Transaction\Category\CreateTransactionCategoryDTO;
use App\DTO\Transaction\Category\UpdateTransactionCategoryDTO;
use App\Helpers\TransactionCategoryHelper;
use App\Repositories\TransactionCategoryRepository;

class TransactionCategoryService
{
    public function __construct(protected TransactionCategoryRepository $repository) {}

    public function create($request)
    {
        TransactionCategoryHelper::existsCategory(
            $request->name,
            'create'
        );

        $categoryDTO = CreateTransactionCategoryDTO::fromRequest([
            ...$request->only(['name']),
        ]);

        return $this->repository->create($categoryDTO->toArray());
    }

    public function update($request)
    {
        TransactionCategoryHelper::existsCategory(
            $request->name,
            'update',
            $request->id
        );

        $categoryDTO = UpdateTransactionCategoryDTO::fromRequest([
            ...$request->only(['name']),
        ]);

        return $this->repository->update($request->id, $categoryDTO->toArray());
    }
}
