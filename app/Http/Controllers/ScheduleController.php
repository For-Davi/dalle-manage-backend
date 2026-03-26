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
use Illuminate\Http\Request;

class ScheduleController extends BaseController
{
    public function __construct(
        private ScheduleService $service,
        private ScheduleRepository $repository
    ) {}

    public function index(Request $request)
    {
        return $this->safeExecute(function () {
            $schedules = $this->repository->getAllByEnterpriseAndPeriod(true, ['category']);

            return response()->json(['schedules' => $schedules], 200);
        }, 'Erro ao buscar agendamentos', $request);
    }

    public function indexPeriod(Request $request)
    {
        return $this->safeExecute(function () {
            $periods = $this->repository->getPeriods();

            return response()->json(['periods' => $periods], 200);
        }, 'Erro ao buscar períodos', $request);
    }

    public function show(ShowScheduleRequest $request)
    {
        return $this->safeExecute(function () use ($request) {
            $schedule = $this->repository->findById($request->route('scheduleID'), ['category']);

            return response()->json(['schedule' => $schedule], 200);
        }, 'Erro ao buscar agendamento', $request);
    }

    public function filter(FilterScheduleRequest $request)
    {
        return $this->safeExecute(function () use ($request) {
            $scheduleFilterDTO = FilterScheduleDTO::fromRequest($request);
            $schedules = $this->repository->getAllWithFilter($scheduleFilterDTO->toArray(), ['category']);

            return response()->json(['schedules' => $schedules], 200);
        }, 'Erro ao filtrar agendamentos', $request);
    }

    public function store(CreateScheduleRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            $this->service->create($request);
            $schedules = $this->repository->getAllByEnterpriseAndPeriod(true, ['category']);

            return response()->json(['schedules' => $schedules, 'message' => 'Agendamento inserido'], 201);
        }, 'Erro ao inserir agendamento', $request);
    }

    public function export(ExportScheduleRequest $request)
    {
        return $this->safeExecute(function () use ($request) {
            return $this->service->export($request);
        }, 'Erro ao exportar agendamentos', $request);
    }

    public function update(UpdateScheduleRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            $this->service->update($request);
            $schedules = $this->repository->getAllByEnterpriseAndPeriod(true, ['category']);

            return response()->json(['schedules' => $schedules, 'message' => 'Agendamento atualizado'], 200);
        }, 'Erro ao atualizar agendamento', $request);
    }

    public function destroy(DeleteScheduleRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            $this->repository->delete($request->route('scheduleID'));
            $schedules = $this->repository->getAllByEnterpriseAndPeriod(true, ['category']);

            return response()->json(['schedules' => $schedules, 'message' => 'Agendamento excluído'], 200);
        }, 'Erro ao excluir agendamento', $request);
    }

    public function finishSchedule(FinishScheduleRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            $this->service->finishSchedule($request);
            $schedules = $this->repository->getAllByEnterpriseAndPeriod(true, ['category']);

            return response()->json(['schedules' => $schedules, 'message' => 'Finalização concluída'], 200);
        }, 'Erro ao finalizar agendamento', $request);
    }
}
