<?php

namespace App\Services;

use App\DTO\Setting\Appearance\UpdateSettingAppearanceDTO;
use App\Repositories\SettingAppearanceRepository;

class SettingAppearanceService
{
    public function __construct(
        private SettingAppearanceRepository $repository
    ) {}

    public function update($request)
    {
        $appearanceDTO = UpdateSettingAppearanceDTO::fromRequest($request);

        return $this->repository->update($appearanceDTO->toArray());
    }
}
