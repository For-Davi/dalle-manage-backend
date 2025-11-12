<?php

namespace App\Services;

use App\DTO\Grid\Item\CreateGridItemDTO;
use App\DTO\Grid\Item\UpdateGridItemDTO;
use App\Repositories\GridItemRepository;

class GridItemService
{
    public function __construct(protected GridItemRepository $repository) {}

    public function create($request)
    {
        $gridItemDTO = CreateGridItemDTO::fromRequest($request);

        return $this->repository->create($gridItemDTO->toArray());
    }

    public function update($request)
    {
        $gridItemDTO = UpdateGridItemDTO::fromRequest($request);

        return $this->repository->update($request->id, $gridItemDTO->toArray());
    }
}
