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

        if (empty($filters['start_date']) && empty($filters['end_date'])) {
            $now = Carbon::now($timezone);
            $start = $now->copy()->startOfMonth()->toDateTimeString();
            $end = $now->copy()->endOfMonth()->toDateTimeString();
        } else {
            $start = ! empty($filters['start_date'])
                ? Carbon::createFromFormat('d-m-Y', $filters['start_date'], $timezone)->startOfDay()->toDateTimeString()
                : null;
            $end = ! empty($filters['end_date'])
                ? Carbon::createFromFormat('d-m-Y', $filters['end_date'], $timezone)->endOfDay()->toDateTimeString()
                : null;
        }

        return DB::table('sales')
            ->join('employees', 'sales.seller_id', '=', 'employees.id')
            ->join('commissions', 'sales.id', '=', 'commissions.sale_id')
            ->select(
                'employees.name as seller_name',
                DB::raw('COUNT(sales.id) as sale_count'),
                DB::raw('SUM(commissions.commission_value) as total_commission'),
                DB::raw("DATE_FORMAT(sales.date, '%m/%Y') as period")
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
            ->groupBy('employees.id', 'employees.name', 'period', 'sales.date')
            ->orderBy('sales.date', 'asc')
            ->get();
    }

    public function getAllBySale($saleID)
    {
        return $this->model->where('sale_id', $saleID)->get();
    }
}
