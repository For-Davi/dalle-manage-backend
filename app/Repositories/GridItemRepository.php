<?php

namespace App\Repositories;

use App\Models\GridItem;
use App\Repositories\Base\BaseRepository;

class GridItemRepository extends BaseRepository
{
    public function __construct(GridItem $model)
    {
        parent::__construct($model);
    }

    public function getAllByGroup($gridGroupID)
    {
        return $this->model
            ->where('grid_group_id', $gridGroupID)
            ->orderBy('order', 'asc')
            ->get();
    }

    public function deleteAllByGroup($groupId)
    {
        $this->model->where('grid_group_id', $groupId)->delete();
    }

    public function delete($id)
    {
        $gridItem = $this->findById($id);

        if ($gridItem) {

            // TODO: Quando criar a tabela de produtos deve descomentar esse codigo abaixo
            // DB::table('catalogs')
            //     ->where('enterprise_id', $color->enterprise_id)
            //     ->where('color_id', $color->id)
            //     ->update(['color_id' => null]);

            return $gridItem->delete();
        }

        return false;
    }
}
