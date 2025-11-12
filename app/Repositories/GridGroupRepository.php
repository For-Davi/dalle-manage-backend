<?php

namespace App\Repositories;

use App\Models\GridGroup;
use Illuminate\Support\Facades\DB;

class GridGroupRepository
{
    public function __construct(protected GridGroup $model) {}

    public function getAllByEnterprise($relations = null)
    {
        $query = $this->model->query();

        if ($relations) {
            $query->with($relations);
        }

        return $query->get();
    }

    public function findById($id)
    {
        return $this->model->find($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $gridGroup = $this->findById($id);
        if ($gridGroup) {
            $gridGroup->update($data);

            return $gridGroup;
        }

        return null;
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
