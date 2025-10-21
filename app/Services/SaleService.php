<?php

namespace App\Services;

use App\DTO\Receipt\CreateReceiptDTO;
use App\DTO\Receipt\UpdateReceiptDTO;
use App\Repositories\ReceiptRepository;

class SaleService
{
    public function __construct(protected ReceiptRepository $repository) {}

    public function create($request)
    {
        $totalValue = $request->input('dataSale.totalPrice')+$request->freightValue+$request->fees;

       dd('dados', $request);
    }
}
