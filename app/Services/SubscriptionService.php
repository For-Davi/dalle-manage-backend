<?php

namespace App\Services;

use App\DTO\Subscription\CreateSubscriptionTestDTO;
use App\Helpers\SubscriptionHelper;
use App\Repositories\EnterpriseRepository;
use Illuminate\Support\Facades\Auth;

class SubscriptionService
{
    public function __construct(
        protected EnterpriseRepository $enterpriseRepository
    ) {}

    public function startTestFree()
    {
        SubscriptionHelper::allowTestFree();

        $enterpriseID = Auth::user()->enterprise_id;
        $dataDTO = CreateSubscriptionTestDTO::fromRequest();

        $result = $this->enterpriseRepository->update($enterpriseID, $dataDTO->toArray());

        return $result;
    }
}
