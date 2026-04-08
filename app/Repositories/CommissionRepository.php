<?php

namespace App\Repositories;

use App\Models\Commission;
use App\Repositories\Base\BaseRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CommissionRepository extends BaseRepository
{
    public function __construct(Commission $model)
    {
        parent::__construct($model);
    }

    public function getAllWithFilter(array $filters)
    {
        $timezone = 'America/Sao_Paulo';

        if (empty($filters['start_period']) && empty($filters['end_period'])) {
            $now = Carbon::now($timezone);
            $start = $now->copy()->startOfYear()->toDateTimeString();
            $end = $now->copy()->endOfYear()->toDateTimeString();
        } else {
            $start = ! empty($filters['start_period'])
                ? Carbon::createFromFormat('m/Y', $filters['start_period'], $timezone)
                    ->startOfMonth()
                    ->startOfDay()
                    ->toDateTimeString()
                : null;
            $end = ! empty($filters['end_period'])
                ? Carbon::createFromFormat('m/Y', $filters['end_period'], $timezone)
                    ->endOfMonth()
                    ->endOfDay()
                    ->toDateTimeString()
                : null;
        }

        return DB::table('sales')
            ->join('employees', 'sales.seller_id', '=', 'employees.id')
            ->join('commissions', 'sales.id', '=', 'commissions.sale_id')
            ->select(
                DB::raw("DATE_FORMAT(sales.date, '%m/%Y') as period"),
                'employees.id as seller_id',
                'employees.name as seller_name',
                DB::raw('COUNT(DISTINCT sales.id) as sales_count'),
                DB::raw('SUM(commissions.commission_value) as total_commission'),
                DB::raw("DATE_FORMAT(sales.date, '%Y%m') as period_sort")
            )
            ->where('sales.enterprise_id', $filters['enterprise_id'])
            ->when(! empty($filters['seller_id']), function ($query) use ($filters) {
                return $query->where('sales.seller_id', $filters['seller_id']);
            })
            ->when($start, function ($query) use ($start) {
                return $query->where('sales.date', '>=', $start);
            })
            ->when($end, function ($query) use ($end) {
                return $query->where('sales.date', '<=', $end);
            })
            ->groupBy(
                'period',
                'period_sort',
                'employees.id',
                'employees.name'
            )
            ->orderBy('period_sort', 'desc')
            ->orderBy('employees.name', 'asc')
            ->get()
            ->map(function ($item) {
                unset($item->period_sort);

                return $item;
            });
    }

    public function getCommissionDetailsBySellerAndPeriod($sellerId, $period)
    {
        return DB::table('sales')
            ->join('commissions', 'sales.id', '=', 'commissions.sale_id')
            ->select(
                'sales.id as sale_id',
                'sales.date as sale_date',
                'sales.client_name',
                'sales.current_total as sale_total',
                'commissions.seller_id',
                'commissions.seller_name',
                'commissions.seller_email',
                DB::raw('SUM(commissions.commission_value) as commission_value'),
                DB::raw("DATE_FORMAT(sales.date, '%m/%Y') as period"),
            )
            ->groupBy(
                'sales.id',
                'sales.date',
                'sales.client_name',
                'sales.current_total',
                'commissions.seller_id',
                'commissions.seller_name',
                'commissions.seller_email',
            )
            ->when($sellerId, fn ($q) => $q->where('commissions.seller_id', $sellerId))
            ->when($period, fn ($q) => $q->whereRaw("DATE_FORMAT(sales.date, '%m/%Y') = ?", [$period]))
            ->orderBy('sales.date', 'desc')
            ->get();
    }

    public function getAllBySale($saleID)
    {
        return $this->model->where('sale_id', $saleID)->get();
    }
}
