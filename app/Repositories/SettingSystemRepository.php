<?php

namespace App\Repositories;

use App\Models\SettingSystem;

class SettingSystemRepository
{
    public function __construct(protected SettingSystem $model) {}

    public function create($data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $system = $this->getByEnterprise($id);
        if ($system) {
            $system->update($data);

            return $system;
        }

        return null;
    }

    public function getByEnterprise(int $enterpriseID)
    {
        return $this->model->where('enterprise_id', $enterpriseID)->first();
    }
}
