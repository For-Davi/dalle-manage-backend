<?php

namespace App\Repositories;

use App\Models\Role;

class RoleRepository
{
    public function __construct(protected Role $model) {}

    public function getAll()
    {
        return $this->model->all();
    }

    public function getAllByEnterprise()
    {
        return $this->model->get();
    }

    public function findById($id)
    {
        return $this->model->find($id);
    }

    public function create($data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $role = $this->findById($id);
        if ($role) {
            $role->update($data);

            return $role;
        }

        return null;
    }

    public function delete($id)
    {
        $role = $this->findById($id);
        if ($role) {

            return $role->delete();
        }

        return false;
    }
}
