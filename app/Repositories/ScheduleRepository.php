<?php

namespace App\Repositories;

use App\Models\Schedule;
use App\Repositories\Base\BaseRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ScheduleRepository extends BaseRepository
{
    public function __construct(Schedule $model)
    {
        parent::__construct($model);
    }

    public function getAllByEnterpriseAndPeriod($onlyPeriodActual = false, array $relations = [])
    {
        $query = $this->model->query();

        if (! empty($relations)) {
            $query->with($relations);
        }

        if ($onlyPeriodActual) {
            $now = Carbon::now();
            $month = str_pad($now->month, 2, '0', STR_PAD_LEFT);
            $year = $now->year;

            $query->where(DB::raw('SUBSTRING(`date`, 4, 2)'), '=', $month)
                ->where(DB::raw('SUBSTRING(`date`, 7, 4)'), '=', $year);
        }

        return $query->get();
    }

    public function getAllWithFilter(array $filters, array $relations = [])
    {
        $query = $this->model->where('enterprise_id', $filters['enterprise_id']);

        if (! is_null($filters['category'])) {
            $query->where('transaction_category_id', $filters['category']);
        }

        if ($filters['type'] !== 'all') {
            $query->where('type', $filters['type']);
        }

        if (empty($filters['period'])) {
            $now = Carbon::now();
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

    public function getPeriods()
    {
        return $this->model
            ->selectRaw("
            DISTINCT
            CONCAT(SUBSTRING(`date`, 4, 2), '-', SUBSTRING(`date`, 7, 4)) as period,
            CAST(SUBSTRING(`date`, 7, 4) AS UNSIGNED) as year_part,
            CAST(SUBSTRING(`date`, 4, 2) AS UNSIGNED) as month_part
        ")
            ->orderBy('year_part', 'ASC')
            ->orderBy('month_part', 'ASC')
            ->pluck('period')
            ->toArray();
    }

    public function delete($id)
    {
        $schedule = $this->findById($id);

        if ($schedule) {
            return $schedule->delete();
        }

        return false;
    }
}
