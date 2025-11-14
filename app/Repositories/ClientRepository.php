<?php

namespace App\Repositories;

use App\DTO\Client\FilterClientDTO;
use App\Models\Client;

class ClientRepository
{
    public function __construct(protected Client $model) {}

    public function getAllByEnterprise()
    {
        return $this->model->get();

    }

    public function findById($id)
    {
        return $this->model->find($id);
    }

    public function getAllWithFilter(FilterClientDTO $filters)
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

        return $query->get();
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $client = $this->findById($id);
        if ($client) {
            $client->update($data);

            return $client;
        }

        return null;
    }

    public function delete($id)
    {
        $client = $this->findById($id);

        if ($client) {
            return $client->delete();
        }

        return false;
    }
}
