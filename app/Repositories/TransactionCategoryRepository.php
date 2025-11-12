<?php

namespace App\Repositories;

use App\Models\TransactionCategory;

class TransactionCategoryRepository
{
    public function __construct(protected TransactionCategory $model) {}

    public function getAllByEnterprise()
    {
        return $this->model->get();
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
        $category = $this->findById($id);
        if ($category) {
            $category->update($data);

            return $category;
        }

        return null;
    }

    public function delete($id)
    {
        $category = $this->findById($id);

        if ($category) {
            return $category->delete();
        }

        return false;
    }
}
