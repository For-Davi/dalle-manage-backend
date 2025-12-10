<?php

namespace App\Http\Controllers;

use App\DTO\Schedule\FilterScheduleDTO;
use App\Http\Requests\Schedule\CreateScheduleRequest;
use App\Http\Requests\Schedule\DeleteScheduleRequest;
use App\Http\Requests\Schedule\ExportScheduleRequest;
use App\Http\Requests\Schedule\FilterScheduleRequest;
use App\Http\Requests\Schedule\FinishScheduleRequest;
use App\Http\Requests\Schedule\ShowScheduleRequest;
use App\Http\Requests\Schedule\UpdateScheduleRequest;
use App\Repositories\ScheduleRepository;
use App\Services\ScheduleService;
use App\Utils\ErrorLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScheduleController
{
    public function __construct(
        private ScheduleService $service,
        private ScheduleRepository $repository
    ) {}

    public function index(Request $request)
    {
        try {
            $schedules = $this->repository->getAllByEnterprise(true, ['category']);

            return response()->json(['schedules' => $schedules], 200);
        } catch (\Exception $e) {
            ErrorLogger::log('Erro ao buscar agendamentos:', $e, $request);

            return response()->json(['message' => 'Erro ao buscar agendamentos'], 500);
        }
    }

    public function indexPeriod(Request $request)
    {
        try {
            $periods = $this->repository->getPeriods();

            return response()->json(['periods' => $periods], 200);
        } catch (\Exception $e) {
            ErrorLogger::log('Erro ao buscar períodos:', $e, $request);

            return response()->json(['message' => 'Erro ao buscar períodos'], 500);
        }
    }

    public function show(ShowScheduleRequest $request)
    {
        try {
            $schedule = $this->repository->findById($request->route('scheduleID'), ['category']);

            return response()->json(['schedule' => $schedule], 200);

        } catch (\Exception $e) {
            ErrorLogger::log('Erro ao buscar agendamento:', $e, $request);

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function filter(FilterScheduleRequest $request)
    {
        try {
            $scheduleFilterDTO = FilterScheduleDTO::fromRequest([
                ...$request->only(['period', 'category', 'type']),
            ]);
            $schedules = $this->repository->getAllWithFilter($scheduleFilterDTO->toArray(), ['category']);

            return response()->json(['schedules' => $schedules], 200);

        } catch (\Exception $e) {
            ErrorLogger::log('Erro ao filtrar agendamentos:', $e, $request);

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function store(CreateScheduleRequest $request)
    {
        try {
            DB::beginTransaction();
            $schedule = $this->service->create($request);
            if ($schedule) {
                DB::commit();

                $schedules = $this->repository->getAllByEnterprise(true, ['category']);

                return response()->json(['schedules' => $schedules, 'message' => 'Agendamento inserido'], 201);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            ErrorLogger::log('Erro ao inserir agendamento:', $e, $request);

            return response()->json(['message' => 'Erro ao inserir agendamento'], 500);
        }
    }

    public function export(ExportScheduleRequest $request)
    {
        try {
            return $this->service->export($request);
        } catch (\Exception $e) {

            ErrorLogger::log('Erro ao exportar agendamentos:', $e, $request);

            return response()->json(['message' => 'Erro ao exportar agendamentos'], 500);
        }
    }

    public function update(UpdateScheduleRequest $request)
    {
        try {
            DB::beginTransaction();
            $schedule = $this->service->update($request);

            if ($schedule) {
                DB::commit();

                $schedules = $this->repository->getAllByEnterprise(true, ['category']);

                return response()->json(['schedules' => $schedules, 'message' => 'Agendamento atualizado'], 200);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            ErrorLogger::log('Erro ao atualizar agendamento:', $e, $request);

            return response()->json(['message' => 'Erro ao atualizar agendamento'], 500);
        }
    }

    public function destroy(DeleteScheduleRequest $request)
    {
        try {
            DB::beginTransaction();

            $schedule = $this->repository->delete($request->route('scheduleID'));

            if ($schedule) {
                DB::commit();
                $schedules = $this->repository->getAllByEnterprise(true, ['category']);

                return response()->json(['schedules' => $schedules, 'message' => 'Agendamento excluído'], 200);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            ErrorLogger::log('Erro ao excluir agendamento:', $e, $request);

            return response()->json(['message' => 'Erro ao excluir agendamento'], 500);
        }
    }

    public function finishSchedule(FinishScheduleRequest $request)
    {
        try {
            DB::beginTransaction();

            $schedule = $this->service->finishSchedule($request);

            if ($schedule) {
                DB::commit();

                $schedules = $this->repository->getAllByEnterprise(true, ['category']);

                return response()->json(['schedules' => $schedules, 'message' => 'Finalização concluída'], 200);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            ErrorLogger::log('Erro ao finalizar agendamento:', $e, $request);

            return response()->json(['message' => 'Erro ao finalizar agendamento'], 500);
        }
    }
}
