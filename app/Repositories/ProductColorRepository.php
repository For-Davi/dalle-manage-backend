<?php

namespace App\Repositories;

use App\Models\ProductColor;
use App\Repositories\Base\BaseRepository;

class ProductColorRepository extends BaseRepository
{
    public function __construct(ProductColor $model)
    {
        parent::__construct($model);
    }

    public function delete($id)
    {
        $color = $this->findById($id);

        if ($color) {

            // TODO: Quando criar a tabela de produtos deve descomentar esse codigo abaixo
            // DB::table('catalogs')
            //     ->where('enterprise_id', $color->enterprise_id)
            //     ->where('color_id', $color->id)
            //     ->update(['color_id' => null]);

            return $color->delete();
        }

        return false;
    }
}
