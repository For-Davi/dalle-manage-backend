<?php

namespace App\Repositories;

use App\Models\ProductTag;
use App\Repositories\Base\BaseRepository;

class ProductTagRepository extends BaseRepository
{
    public function __construct(ProductTag $model)
    {
        parent::__construct($model);
    }

    public function getAllByTagID($tagID)
    {
        return $this->model->where('tag_id', $tagID)->get();
    }

    public function getAllByProductAndTag($productID, $tagID)
    {
        return $this->model
            ->where('product_id', $productID)
            ->where('tag_id', $tagID)
            ->first();
    }

    public function deleteByProductID($productID)
    {
        return $this->model
            ->where('product_id', $productID)
            ->delete();
    }

    public function deleteByTagID($tagID)
    {
        return $this->model
            ->where('tag_id', $tagID)
            ->delete();
    }

    public function deleteByProductAndTag($productID, $tagID)
    {
        return $this->model
            ->where('product_id', $productID)
            ->where('tag_id', $tagID)
            ->delete();
    }
}
