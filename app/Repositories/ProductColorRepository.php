<?php

namespace App\Repositories;

use App\Models\ProductColor;

class ProductColorRepository
{
    public function __construct(protected ProductColor $model) {}

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
        $color = $this->findById($id);
        if ($color) {
            $color->update($data);

            return $color;
        }

        return null;
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
