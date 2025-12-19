<?php

namespace App\Rules;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;

class MaxDateRange implements Rule
{
    protected int $maxYears;

    protected string $errorMessage = '';

    public function __construct(int $maxYears = 4)
    {
        $this->maxYears = $maxYears;
    }

    public function passes($attribute, $value)
    {
        $startDate = request()->input('startDate');

        if (! $startDate || ! $value) {
            return true;
        }

        try {
            $tz = 'America/Sao_Paulo';
            [$startMonth, $startYear] = explode('-', $startDate);
            [$endMonth, $endYear] = explode('-', $value);

            $start = Carbon::createFromDate($startYear, $startMonth, 1, $tz)->startOfMonth();
            $end = Carbon::createFromDate($endYear, $endMonth, 1, $tz)->endOfMonth();
            if ($start->gt($end)) {
                $this->errorMessage = 'A data inicial não pode ser maior que a data final.';

                return false;
            }
            if ($start->diffInMonths($end) > ($this->maxYears * 12)) {
                $this->errorMessage = "O intervalo entre as datas não pode ser maior que {$this->maxYears} anos.";

                return false;
            }

            return true;
        } catch (\Throwable $e) {
            $this->errorMessage = 'As datas informadas são inválidas.';

            return false;
        }
    }

    public function message()
    {
        return $this->errorMessage ?: 'Intervalo de datas inválido.';
    }
}
