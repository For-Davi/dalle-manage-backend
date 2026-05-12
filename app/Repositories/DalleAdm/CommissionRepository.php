<?php

namespace App\Repositories\DalleAdm;

use App\Models\DalleAdm\Commission;
use App\Repositories\Base\BaseRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CommissionRepository extends BaseRepository
{
    public function __construct(Commission $model)
    {
        parent::__construct($model);
    }

    public function getAllWithFilter(array $filters)
    {
        if (empty($filters['start_period']) && empty($filters['end_period'])) {
            $now = Carbon::now();
            $start = $now->copy()->startOfYear()->toDateTimeString();
            $end = $now->copy()->endOfYear()->toDateTimeString();
        } else {
            $start = ! empty($filters['start_period'])
                ? Carbon::createFromFormat('m/Y', $filters['start_period'])
                    ->startOfMonth()
                    ->startOfDay()
                    ->toDateTimeString()
                : null;

            $end = ! empty($filters['end_period'])
                ? Carbon::createFromFormat('m/Y', $filters['end_period'])
                    ->endOfMonth()
                    ->endOfDay()
                    ->toDateTimeString()
                : null;
        }

        return Commission::query()->where('seller_id', Auth::user()->id)
            ->select(
                DB::raw("DATE_FORMAT(created_at, '%m/%Y') as period"),
                DB::raw('COUNT(*) as sales_count'),
                DB::raw('SUM(commission_value) as total_commission'),
                DB::raw("DATE_FORMAT(created_at, '%Y%m') as period_sort")
            )
            ->when($start, function ($query) use ($start) {
                return $query->where('created_at', '>=', $start);
            })
            ->when($end, function ($query) use ($end) {
                return $query->where('created_at', '<=', $end);
            })
            ->groupBy('period', 'period_sort')
            ->orderBy('period_sort', 'desc')
            ->get()
            ->map(function ($item) {
                unset($item->period_sort);

                return $item;
            });
    }
}
