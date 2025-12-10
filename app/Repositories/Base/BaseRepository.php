<?php

namespace App\Repositories\Base;

use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data, array $relations = []): ?Model
    {
        // Utiliza um find diferente
        if ($this->model instanceof \App\Models\ProductAdvanced) {
            $record = $this->findByProduct($id, $relations);
        } else {
            $record = $this->findById($id, $relations);
        }

        if (! $record) {
            return null;
        }

        $record->update($data);

        return $record;
    }

    public function getAllByEnterprise(array $relations = [], array $columns = ['*'], array $filters = [])
    {
        $query = $this->model->query();

        if (! empty($relations)) {
            $query->with($relations);
        }

        if (! empty($filters)) {
            foreach ($filters as $field => $value) {
                $query->where($field, $value);
            }
        }

        return $query->get($columns);
    }

    public function getAllByUser(array $relations = [])
    {
        $query = $this->model->query();

        if (! empty($relations)) {
            $query->with($relations);
        }

        // Só aplica orderBy se o model for Notification
        if ($this->model instanceof \App\Models\Notification) {
            $query->orderBy('created_at', 'desc');
        }

        return $query->get();
    }

    public function findByCpf(string $cpf, array $relations = []): ?Model
    {
        $query = $this->model->newQuery();

        if (! empty($relations)) {
            $query->with($relations);
        }

        return $query->where('cpf', $cpf)->first();
    }

    public function findByProduct(string $productID, array $relations = []): ?Model
    {
        $query = $this->model->newQuery();

        if (! empty($relations)) {
            $query->with($relations);
        }

        return $query->where('product_id', $productID)->first();
    }

    public function findByCnpj(string $cnpj, array $relations = []): ?Model
    {
        $query = $this->model->newQuery();

        if (! empty($relations)) {
            $query->with($relations);
        }

        return $query->where('cnpj', $cnpj)->first();
    }

    public function findById(int $id, array $relations = []): ?Model
    {
        $query = $this->model->newQuery();

        if (! empty($relations)) {
            $query->with($relations);
        }

        return $query->find($id);
    }
}
