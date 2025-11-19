<?php

namespace App\Repositories;

use App\Models\ProductImage;
use App\Repositories\Base\BaseRepository;

class ProductImageRepository extends BaseRepository
{
    public function __construct(ProductImage $model)
    {
        parent::__construct($model);
    }

    public function getAllByImageID($imageID)
    {
        return $this->model->where('image_id', $imageID)->get();
    }

    public function getAllByProductAndImage($productID, $imageID)
    {
        return $this->model
            ->where('product_id', $productID)
            ->where('image_id', $imageID)
            ->first();
    }

    public function deleteByProductID($productID)
    {
        return $this->model
            ->where('product_id', $productID)
            ->delete();
    }

    public function deleteByTagID($imageID)
    {
        return $this->model
            ->where('image_id', $imageID)
            ->delete();
    }

    public function deleteByProductAndImage($productID, $imageID)
    {
        return $this->model
            ->where('product_id', $productID)
            ->where('image_id', $imageID)
            ->delete();
    }
}
