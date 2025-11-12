<?php

namespace App\Repositories;

use App\Models\SettingSystem;
use Illuminate\Support\Facades\Auth;

class SettingSystemRepository
{
    public function __construct(protected SettingSystem $model) {}

    public function create($data)
    {
        return $this->model->create($data);
    }

    public function update(array $data)
    {
        $system = $this->getByEnterprise(Auth::user()->enterprise_id);
        if ($system) {
            $system->update($data);

            return $system;
        }

        return null;
    }

    public function getByEnterprise()
    {
        return $this->model->first();
    }
}
