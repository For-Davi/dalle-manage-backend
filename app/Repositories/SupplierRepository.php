<?php

namespace App\Repositories;

use App\DTO\Supplier\FilterSupplierDTO;
use App\Models\Supplier;
use App\Repositories\Base\BaseRepository;

class SupplierRepository extends BaseRepository
{
    public function __construct(Supplier $model)
    {
        parent::__construct($model);
    }

    public function getAllWithFilter(FilterSupplierDTO $filters)
    {
        $query = $this->model->where('enterprise_id', $filters->enterprise_id);

        if ($filters->name !== null) {
            $query->where('name', 'like', "%{$filters->name}%");
        }

        if ($filters->email !== null) {
            $query->where('email', 'like', "%{$filters->email}%");
        }
        if ($filters->cpf !== null) {
            $query->where('cpf', 'like', "%{$filters->cpf}%");
        }

        if ($filters->cnpj !== null) {
            $query->where('cnpj', 'like', "%{$filters->cnpj}%");
        }

        if ($filters->country !== null) {
            $query->where('country', 'like', "%{$filters->country}%");
        }

        if ($filters->state !== null) {
            $query->where('state', 'like', "%{$filters->state}%");
        }

        if ($filters->city !== null) {
            $query->where('city', 'like', "%{$filters->city}%");
        }

        if ($filters->active !== null) {
            $query->where('active', $filters->active);
        }

        if ($filters->category_id !== null) {
            $query->where('category_supplier_id', $filters->category_id);
        }

        return $query->get();
    }

    public function delete($id)
    {
        $supplier = $this->findById($id);

        if ($supplier) {
            return $supplier->delete();
        }

        return false;
    }
}
