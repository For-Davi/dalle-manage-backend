<?php

namespace App\Services;

use App\DTO\Movement\CreateOrUpdateMovementDTO;
use App\DTO\Movement\FilterMovementDTO;
use App\Exports\Movement\MovementsExport;
use App\Repositories\MovementRepository;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class MovementService
{
    public function __construct(protected MovementRepository $repository) {}

    public function create($request)
    {
        $requestDate = Carbon::createFromFormat('d/m/Y', $request->date);

        if ($request->quantity > 1) {
            $movements = [];
            $startDate = Carbon::createFromFormat('d/m/Y', $request->date);

            for ($i = 0; $i < $request->quantity; $i++) {
                $date = (clone $startDate)->addMonths($i);

                $day = $startDate->day;
                if ($day > $date->daysInMonth) {
                    $date->day($date->daysInMonth);
                }

                $movementDTO = CreateOrUpdateMovementDTO::fromRequest(
                    $request,
                    ['date' => $date->format('d-m-Y')]
                );

                $movements[] = $this->repository->create($movementDTO->toArray());
            }

            return $movements;
        }

        $movementDTO = CreateOrUpdateMovementDTO::fromRequest(
            $request,
            ['date' => $requestDate->format('d-m-Y')]
        );

        return $this->repository->create($movementDTO->toArray());
    }

    public function update($request)
    {
        $requestDate = Carbon::createFromFormat('d/m/Y', $request->date);

        $movementDTO = CreateOrUpdateMovementDTO::fromRequest(
            $request,
            ['date' => $requestDate->format('d-m-Y')]
        );

        return $this->repository->update($request->id, $movementDTO->toArray());
    }

    public function export($request)
    {
        $dateTime = now()->format('Ymd_His');

        $exportMovementDTO = FilterMovementDTO::fromRequest($request);
        $movements = $this->repository->getAllWithFilter($exportMovementDTO->toArray(), ['category']);

        if ($request->format === 'excel') {
            $fileName = "movements_{$dateTime}.xlsx";

            return (new MovementsExport($movements))->download($fileName);
        } else {
            $fileName = "movements_{$dateTime}.xlsx";

            $pdf = Pdf::loadView('exports.movements-pdf', [
                'movements' => $movements,
            ]);

            return $pdf->download($fileName);
        }
    }
}
