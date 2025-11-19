<?php

namespace App\Repositories;

use App\Models\Image;
use App\Repositories\Base\BaseRepository;
use Illuminate\Support\Facades\DB;

class ImageRepository extends BaseRepository
{
    public function __construct(Image $model)
    {
        parent::__construct($model);
    }

    public function delete($id)
    {
        $image = $this->findById($id);

        if ($image) {
            DB::table('product_image')->where('image_id', $image->id)->delete();

            return $image->delete();
        }

        return false;
    }
}
