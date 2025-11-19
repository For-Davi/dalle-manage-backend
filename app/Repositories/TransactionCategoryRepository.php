<?php

namespace App\Repositories;

use App\Models\TransactionCategory;
use App\Repositories\Base\BaseRepository;

class TransactionCategoryRepository extends BaseRepository
{
    public function __construct(TransactionCategory $model)
    {
        parent::__construct($model);
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
