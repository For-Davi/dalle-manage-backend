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

    public function getAllWithFilter(array $filters)
    {
        $tz = 'America/Sao_Paulo';
        $query = $this->model
            ->where('enterprise_id', $filters['enterprise_id']);

        if (! empty($filters['start_date']) && ! empty($filters['end_date'])) {

            [$startMonth, $startYear] = explode('-', $filters['start_date']);
            [$endMonth, $endYear] = explode('-', $filters['end_date']);

            $start = Carbon::createFromDate($startYear, $startMonth, 1, $tz)
                ->startOfMonth()
                ->format('d-m-Y H:i:s');

            $end = Carbon::createFromDate($endYear, $endMonth, 1, $tz)
                ->endOfMonth()
                ->format('d-m-Y H:i:s');

            $query->whereRaw(
                "STR_TO_DATE(date, '%d-%m-%Y %H:%i:%s')
             BETWEEN STR_TO_DATE(?, '%d-%m-%Y %H:%i:%s')
             AND STR_TO_DATE(?, '%d-%m-%Y %H:%i:%s')",
                [$start, $end]
            );
        }

        if (! empty($filters['seller'])) {
            $query->where('seller_id', $filters['seller']);
        }

        if (! empty($filters['product'])) {
            $query->whereHas('items', fn ($q) => $q->where('product_name', $filters['product'])
            );
        }

        if (! empty($filters['category'])) {
            $query->whereHas('items.product.product', fn ($q) => $q->where('product_category_id', (int) $filters['category'])
            );
        }

        if (! empty($filters['type_receipt'])) {
            $query->whereHas('payment', fn ($q) => $q->where('payment_method_id', $filters['type_receipt'])
            );
        }

        return $query->get();
    }

    public function getTodayData()
    {
        $sales = DB::table('sales')->where('date', Carbon::now('America/Sao_Paulo')->format('d-m-Y'));

        return $sales;
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
