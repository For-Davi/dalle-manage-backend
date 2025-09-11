<?php

namespace App\Services;

use App\DTO\Setting\System\UpdateSettingSystemDTO;
use App\Repositories\SettingSystemRepository;

class SettingSystemService
{
    public function __construct(
        private SettingSystemRepository $repository
    ) {}

    public function update($request)
    {
        $systemDTO = UpdateSettingSystemDTO::fromRequest([
            ...$request->only([
                'sendNotificationStockCritical',
            ]),
        ]);

        return $this->repository->update($request->get('enterprise_id'), $systemDTO->toArray());
    }
}
