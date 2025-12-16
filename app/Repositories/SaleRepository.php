<?php

namespace App\Repositories;

use App\Models\Sale;
use App\Repositories\Base\BaseRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SaleRepository extends BaseRepository
{
    public function __construct(Sale $model, protected SaleItemRepository $saleItemRepository)
    {
        parent::__construct($model);
    }

    public function findSaleItensBySaleId($id)
    {
        return $this->saleItemRepository->findBySaleId($id);
    }

    public function getTodayData()
    {   
        $sales = DB::table('sales')->where('date', Carbon::now('America/Sao_Paulo')->format('d-m-Y'));

        return $sales;
    }

    public function getSalesBetweenDates(string $startDate, string $endDate)
{
    return $this->getAllByEnterprise()
        ->filter(function ($sale) use ($startDate, $endDate) {
            $saleDate = Carbon::createFromFormat('d-m-Y H:i:s', $sale->date);
            return $saleDate->between(
                Carbon::createFromFormat('d-m-Y', $startDate)->startOfDay(),
                Carbon::createFromFormat('d-m-Y', $endDate)->endOfDay()
            );
        });
}

        public function getAllWithFilter(array $filters, array $relations = [])
    {
        $query = $this->model->where('enterprise_id', $filters['enterprise_id']);

        if (empty($filters['period'])) {
            $now = Carbon::now('America/Sao_Paulo');
            $month = str_pad($now->month, 2, '0', STR_PAD_LEFT);
            $year = $now->year;
        } else {
            [$month, $year] = explode('/', $filters['period']);
            $month = str_pad($month, 2, '0', STR_PAD_LEFT);
        }

        $query->where(DB::raw('SUBSTRING(`date`, 4, 2)'), '=', $month)
            ->where(DB::raw('SUBSTRING(`date`, 7, 4)'), '=', $year);

        if (! empty($relations)) {
            $query->with($relations);
        }

        return $query->get();
    }

    public function getCouponInfos($id)
    {
        $products = $this->findSaleItensBySaleId($id);

        $firstProduct = $products[0]->load(['sale', 'sale.client', 'sale.enterprise']);

        return [
            'sale' => $firstProduct->sale,
            'enterprise' => $firstProduct->sale->enterprise,
            'client' => $firstProduct->sale->client,
            'products' => $products,
        ];
    }
}
