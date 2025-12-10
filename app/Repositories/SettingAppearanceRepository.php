<?php

namespace App\Repositories;

use App\Models\SettingAppearance;
use Illuminate\Support\Facades\Auth;

class SettingAppearanceRepository
{
    public function __construct(protected SettingAppearance $model) {}

    public function create($data)
    {
        return $this->model->create($data);
    }

    public function update(array $data)
    {
        $appearance = $this->getByEnterprise(Auth::user()->enterprise_id);
        if ($appearance) {
            $appearance->update($data);

            return $appearance;
        }

        return null;
    }

    public function getByEnterprise()
    {
        return $this->model->first();
    }
}
