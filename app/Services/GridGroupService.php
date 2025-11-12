<?php

namespace App\Services;

use App\DTO\Grid\Group\CreateGridGroupDTO;
use App\DTO\Grid\Group\UpdateGridGroupDTO;
use App\DTO\Grid\Item\CreateGridItemDTO;
use App\DTO\Grid\Item\UpdateGridItemDTO;
use App\Repositories\GridGroupRepository;
use App\Repositories\GridItemRepository;

class GridGroupService
{
    public function __construct(
        protected GridGroupRepository $repository,
        protected GridItemRepository $gridItemRepository
    ) {}

    public function create($request)
    {
        $gridGroupDTO = CreateGridGroupDTO::fromRequest($request);

        $gridGroup = $this->repository->create($gridGroupDTO->toArray());
        $this->createItem($gridGroup->id, $request->items, 'create');

        return true;
    }

    private function createItem(int $gridGroupID, array $items, $mode)
    {
        if ($mode === 'create') {
            foreach ($items as $item) {
                $gridItemDTO = CreateGridItemDTO::fromRequest([
                    'size' => $item['size'],
                    'order' => $item['order'],
                    'gridGroupID' => $gridGroupID,
                ]);

                $this->gridItemRepository->create($gridItemDTO->toArray());
            }
        }
        if ($mode === 'update') {
            foreach ($items as $item) {
                $gridItemDTO = UpdateGridItemDTO::fromRequest([
                    'size' => $item['size'],
                    'order' => $item['order'],
                    'active' => $item['active'],
                    'gridGroupID' => $gridGroupID,
                ]);

                $this->gridItemRepository->create($gridItemDTO->toArray());
            }
        }
    }

    public function update($request)
    {
        $gridGroupDTO = UpdateGridGroupDTO::fromRequest($request);

        $this->repository->update($request->id, $gridGroupDTO->toArray());

        $this->gridItemRepository->deleteAllByGroup($request->id);
        $this->createItem($request->id, $request->items, 'update');

        return true;
    }
}
