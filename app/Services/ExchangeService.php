<?php

namespace App\Services;

use App\DTO\Exchange\CreateExchangeDTO;
use App\Helpers\SaleHelper;
use Illuminate\Support\Facades\Auth;
use App\Repositories\ClientRepository;

class ExchangeService
{
    public function __construct(protected ClientRepository $repository) {}

    public function create($request)
    {
        SaleHelper::existsSale($request['saleID'], Auth::user()->enterprise_id);

        //Lógica da criação do exchange
    }
}
