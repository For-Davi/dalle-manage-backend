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

        $hasStart = ! empty($filters['start_date']);
        $hasEnd = ! empty($filters['end_date']);

        if ($hasStart && ! $hasEnd) {
            $start = Carbon::createFromFormat('d/m/Y', $filters['start_date'], $tz)->startOfDay();

            $query->where('date', '>=', $start);
        }

        if ($hasEnd && ! $hasStart) {
            $end = Carbon::createFromFormat('d/m/Y', $filters['end_date'], $tz)->endOfDay();

            $query->where('date', '<=', $end);
        }
        if ($hasStart && $hasEnd) {
            $start = Carbon::createFromFormat('d/m/Y', $filters['start_date'], $tz)->startOfDay();
            $end = Carbon::createFromFormat('d/m/Y', $filters['end_date'], $tz)->endOfDay();

            $query->whereBetween('date', [$start, $end]);
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

    public function deleteSale($id)
    {
        $sale = $this->findById($id);

        if ($sale) {
            DB::table('sale_itens')->where('sale_id', $id)->delete();
            DB::table('sale_deliveries')->where('sale_id', $id)->delete();
            DB::table('sale_payments_methods')->where('sale_id', $id)->delete();
            DB::table('sale_cancellations')->where('sale_id', $id)->delete();
            DB::table('commissions')->where('sale_id', $id)->delete();

            $returns = DB::table('returns')->where('sale_id', $id)->get();

            foreach ($returns as $return) {

                $exchanges = DB::table('exchanges')
                    ->where('return_id', $return->id)
                    ->get();

                foreach ($exchanges as $exchange) {
                    DB::table('exchange_payments_methods')
                        ->where('exchange_id', $exchange->id)
                        ->delete();

                    DB::table('exchange_additional')
                        ->where('exchange_id', $exchange->id)
                        ->delete();

                    DB::table('exchanges')
                        ->where('id', $exchange->id)
                        ->delete();
                }

                DB::table('return_items')
                    ->where('return_id', $return->id)
                    ->delete();

                DB::table('return_exchange_items')
                    ->where('return_id', $return->id)
                    ->delete();

                DB::table('product_movements')
                    ->where('return_id', $return->id)
                    ->delete();

                DB::table('returns')
                    ->where('id', $return->id)
                    ->delete();
            }

            DB::table('product_movements')
                ->where('sale_id', $id)
                ->delete();

            return $sale->delete();
        }

        return null;
    }
}
