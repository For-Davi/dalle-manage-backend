<?php

namespace App\Repositories;

use App\Models\Movement;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MovementRepository
{
    public function __construct(protected Movement $model) {}

    public function getAllByEnterprise($onlyPeriodActual = false, array $relations = [])
    {
        $query = $this->model->query();

        if (! empty($relations)) {
            $query->with($relations);
        }

        if ($onlyPeriodActual) {
            $now = Carbon::now('America/Sao_Paulo');
            $month = str_pad($now->month, 2, '0', STR_PAD_LEFT);
            $year = $now->year;

            $query->where(DB::raw('SUBSTRING(`date`, 4, 2)'), '=', $month)
                ->where(DB::raw('SUBSTRING(`date`, 7, 4)'), '=', $year);
        }

        return $query->get();
    }

    public function findById($id, array $relations = [])
    {
        $query = $this->model;

        if (! empty($relations)) {
            $query = $query->with($relations);
        }

        return $query->find($id);
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

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $movement = $this->findById($id);
        if ($movement) {
            $movement->update($data);

            return $movement;
        }

        return null;
    }

    public function delete($id)
    {
        $movement = $this->findById($id);

        if ($movement) {
            return $movement->delete();
        }

        return false;
    }
}
