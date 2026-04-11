<?php

namespace App\Http\Controllers;

use App\Http\Requests\Delivery\CreatePartialDeliveryRequest;
use App\Http\Requests\Delivery\CreateScheduledDeliveryRequest;
use App\Http\Requests\Delivery\FilterDeliveryRequest;
use App\Http\Requests\Delivery\ShowDeliveryRequest;
use App\Http\Requests\Delivery\UpdateDeliveryRequest;
use App\Http\Resources\Delivery\DeliveryResource;
use App\Repositories\SaleDeliveryRepository;
use App\Services\DeliveryService;
use Illuminate\Http\Request;

class DeliveryController extends BaseController
{
    public function __construct(
        private SaleDeliveryRepository $repository,
        private DeliveryService $service,
    ) {}

    public function index(Request $request)
    {
        return $this->safeExecute(function () use ($request) {
            $deliveries = $this->repository->getDeliveries($request->status);

            return response()->json(['deliveries' => $deliveries], 200);
        }, 'Erro ao buscar as entregas', $request);
    }

    public function getDashboard(Request $request)
    {
        return $this->safeExecute(function () {
            $dashboard = $this->repository->getDeliveryStats();

            return response()->json(['dashboard' => $dashboard], 200);
        }, 'Erro ao buscar dados do dashboard de entregas', $request);
    }

    public function show(ShowDeliveryRequest $request)
    {
        return $this->safeExecute(function () use ($request) {
            $delivery = $this->repository->findById($request->route('deliveryID'));

            return response()->json(['delivery' => new DeliveryResource($delivery)], 200);
        }, 'Erro ao buscar entrega', $request);
    }

    public function filter(FilterDeliveryRequest $request)
    {
        return $this->safeExecute(function () use ($request) {
            $deliveries = $this->repository->getDeliveries($request->query('status', 'all'), $request->filter);

            return response()->json(['deliveries' => $deliveries], 200);
        }, 'Erro ao filtrar entregas', $request);
    }

    public function schedule(CreateScheduledDeliveryRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            check_permission('delivery.update');
            $this->service->schedule($request);
            $status = $request->query('status', 'all');
            $deliveries = $this->repository->getDeliveries($status);

            return response()->json(['deliveries' => $deliveries], 200);
        }, 'Erro ao agendar entrega', $request);
    }

    public function partialDelivered(CreatePartialDeliveryRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            check_permission('delivery.update');
            $this->service->partialDelivered($request);
            $status = $request->query('status', 'all');
            $deliveries = $this->repository->getDeliveries($status);

            return response()->json(['deliveries' => $deliveries, 'message' => 'Entrega marcada como entregue parcialmente'], 200);
        }, 'Erro ao marcar a entrega como entregue parcialmente', $request);
    }

    public function update(UpdateDeliveryRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            check_permission('delivery.update');
            $this->service->update($request);
            $status = $request->query('status', 'all');
            $deliveries = $this->repository->getDeliveries($status);

            return response()->json(['deliveries' => $deliveries, 'message' => 'Entrega atualizada'], 200);
        }, 'Erro ao marcar atualizar entrega', $request);
    }
}
