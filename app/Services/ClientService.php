<?php

namespace App\Services;

use App\DTO\Client\CreateClientDTO;
use App\DTO\Client\UpdateClientDTO;
use App\Helpers\ClientHelper;
use App\Helpers\SaleHelper;
use App\Repositories\ClientRepository;
use App\Repositories\SaleRepository;
use App\Repositories\SettingSystemRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ClientService
{
    public function __construct(
        protected ClientRepository $repository,
        protected SaleRepository $saleRepository,
        protected SettingSystemRepository $settingSystemRepository,
    ) {}

    public function create($request)
    {
        $clientDTO = CreateClientDTO::fromRequest($request);

        return $this->repository->create($clientDTO->toArray());
    }

    public function update($request)
    {
        $supplierDTO = UpdateClientDTO::fromRequest($request);

        return $this->repository->update($request->id, $supplierDTO->toArray());
    }

    public function getCredit($request)
    {
        SaleHelper::existsSale($request['saleID'], Auth::user()->enterprise_id);

        $sale = $this->saleRepository->findById($request['saleID']);

        if ($sale->client_id) {
            $client = $this->repository->findById($sale->client_id);

            return $client->credits;
        }

        return 0;
    }

    public function updateCredit($saleID, $credit, $clientID, $field, $status)
    {
        if (! $clientID && $saleID) {
            $sale = $this->saleRepository->findById($saleID);
            $clientID = $sale->client_id ?? null;
        }

        ClientHelper::existsClient($clientID, $field);

        $client = $this->repository->findById($clientID);

        if($status === 'decrease'){
            ClientHelper::validateCredit($client->id, $credit, $field);

            $updateData = [
                'credits' => 0,
                'credit_expires_at' => null,
            ];
        } else {
            $setting = $this->settingSystemRepository->getByEnterprise(Auth::user()->enterprise_id);

            $updateData = [
            'credits' => $client->credits + $credit,
            'credit_expires_at' => null,
        ];

        if ($setting->has_credit_expired_data) {
            $updateData['credit_expires_at'] = Carbon::now()->addDays($setting->quantity_credit_expire_days);
        }
        }

        return $this->repository->update($client->id, $updateData);
    }
}
