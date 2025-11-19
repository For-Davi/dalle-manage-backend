<?php

namespace App\Repositories;

use App\Models\GridGroup;
use App\Repositories\Base\BaseRepository;
use Illuminate\Support\Facades\DB;

class GridGroupRepository extends BaseRepository
{
    public function __construct(GridGroup $model)
    {
        parent::__construct($model);
    }

    public function getAllByEnterprise($relations = null)
    {
        $query = $this->model->query();

        if ($relations) {
            $query->with($relations);
        }

        return $query->get();
    }

    public function delete($id)
    {
        $gridGroup = $this->findById($id);

        if ($gridGroup) {

            DB::table('grid_items')
                ->where('grid_group_id', $gridGroup->id)
                ->delete();

            return $gridGroup->delete();
        }

        return false;
    }
}
