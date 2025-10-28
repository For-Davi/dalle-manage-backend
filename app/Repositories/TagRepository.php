<?php

namespace App\Repositories;

use App\DTO\Tag\FilterTagDTO;
use App\Models\Tag;

class TagRepository
{
    public function __construct(protected Tag $model) {}

    public function getAllByEnterprise()
    {
        return $this->model->get();
    }

    public function findById($id)
    {
        return $this->model->find($id);
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

    public function create($data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $tag = $this->findById($id);
        if ($tag) {
            $tag->update($data);

            return $tag;
        }

        return null;
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
