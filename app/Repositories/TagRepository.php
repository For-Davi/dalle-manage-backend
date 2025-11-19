<?php

namespace App\Repositories;

use App\DTO\Tag\FilterTagDTO;
use App\Models\Tag;
use App\Repositories\Base\BaseRepository;

class TagRepository extends BaseRepository
{
    public function __construct(Tag $model)
    {
        parent::__construct($model);
    }

    public function getAllWithFilter(FilterTagDTO $filters)
    {
        $query = $this->model->newQuery();

        if ($filters->name !== null) {
            $query->where('name', 'like', "%{$filters->name}%");
        }

        if ($filters->active !== null) {
            $query->where('active', $filters->active);
        }

        return $query->get();
    }

    public function delete($id)
    {
        $tag = $this->findById($id);
        if ($tag) {

            return $tag->delete();
        }

        return false;
    }
}
