<?php

namespace App\Repositories;

use App\Models\SaleCancellation;
use App\Repositories\Base\BaseRepository;

class SaleCancellationRepository extends BaseRepository
{
    protected SaleRepository $saleRepository;

    public function __construct(SaleCancellation $model, SaleRepository $saleRepository)
    {
        parent::__construct($model);
        $this->saleRepository = $saleRepository;
    }

    public function findBySaleId($id)
    {
        $sale = $this->saleRepository->findById($id);

        if ($sale) {
            return $this->findById($id);
        }

        return null;
    }
}
