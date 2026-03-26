<?php

namespace App\Services;

use App\DTO\Movement\CreateOrUpdateMovementDTO;
use App\DTO\Schedule\CreateOrUpdateScheduleDTO;
use App\DTO\Schedule\FilterScheduleDTO;
use App\Exports\Schedule\SchedulesExport;
use App\Repositories\MovementRepository;
use App\Repositories\ScheduleRepository;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ScheduleService
{
    public function __construct(protected ScheduleRepository $repository, protected MovementRepository $movementrepository) {}

    public function create($request)
    {
        $requestDate = Carbon::createFromFormat('d/m/Y', $request->date);

        if ($request->quantity > 1) {
            $schedules = [];
            $startDate = Carbon::createFromFormat('d/m/Y', $request->date);

            for ($i = 0; $i < $request->quantity; $i++) {
                $date = (clone $startDate)->addMonths($i);

                $day = $startDate->day;
                if ($day > $date->daysInMonth) {
                    $date->day($date->daysInMonth);
                }

                $scheduleDTO = CreateOrUpdateScheduleDTO::fromRequest([
                    $request,
                    'date' => $date->format('d-m-Y'),
                ]);

                $schedules[] = $this->repository->create($scheduleDTO->toArray());
            }

            return $schedules;
        }

        $scheduleDTO = CreateOrUpdateScheduleDTO::fromRequest([
            $request,
            'date' => $requestDate->format('d-m-Y'),
        ]);

        return $this->repository->create($scheduleDTO->toArray());
    }

    public function update($request)
    {
        $requestDate = Carbon::createFromFormat('d/m/Y', $request->date);

        $scheduleDTO = CreateOrUpdateScheduleDTO::fromRequest([
            $request,
            'date' => $requestDate->format('d-m-Y'),
        ]);

        return $this->repository->update($request->id, $scheduleDTO->toArray());
    }

    public function finishSchedule($request)
    {
        $schedule = $this->repository->findById($request->scheduleID);

        if (! $schedule) {
            return null;
        }

        if ($request->close === 'dateSchedule') {
            $dateFormatted = Carbon::parse($schedule->date)->format('d-m-Y');

            $movementDTO = CreateOrUpdateMovementDTO::fromRequest([
                'value' => $schedule->value,
                'transactionCategoryID' => $schedule->transaction_category_id,
                'description' => $schedule->description,
                'type' => $schedule->type,
                'enterpriseID' => $schedule->enterprise_id,
                'date' => $dateFormatted,
            ]);
            $this->repository->delete($schedule->id);
            $this->movementrepository->create($movementDTO->toArray());

            return $schedule;
        }

        if ($request->close === 'dateNow') {
            $dateFormatted = Carbon::parse($schedule->date);

            $today = now();
            $dateFormatted->month($today->month)->year($today->year);

            $movementDTO = CreateOrUpdateMovementDTO::fromRequest([
                'value' => $schedule->value,
                'transactionCategoryID' => $schedule->transaction_category_id,
                'description' => $schedule->description,
                'type' => $schedule->type,
                'enterpriseID' => $schedule->enterprise_id,
                'date' => $dateFormatted->format('d-m-Y'),
            ]);
            $this->repository->delete($schedule->id);
            $this->movementrepository->create($movementDTO->toArray());

            return $schedule;
        }
    }

    public function export($request)
    {
        $dateTime = now()->format('Ymd_His');

        $exportScheduleDTO = FilterScheduleDTO::fromRequest([
            ...$request->only(['period', 'category', 'type']),
        ]);
        $schedules = $this->repository->getAllWithFilter($exportScheduleDTO->toArray(), ['category']);

        if ($request->format === 'excel') {
            $fileName = "schedules_{$dateTime}.xlsx";

            return (new SchedulesExport($schedules))->download($fileName);
        } else {
            $fileName = "schedules_{$dateTime}.xlsx";

            $pdf = Pdf::loadView('exports.schedules-pdf', [
                'schedules' => $schedules,
            ]);

            return $pdf->download($fileName);
        }
    }
}
