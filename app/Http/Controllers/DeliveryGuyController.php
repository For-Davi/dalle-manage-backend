<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeliveryGuy\CreateDeliveryGuyRequest;
use App\Http\Requests\DeliveryGuy\DeleteDeliveryGuyRequest;
use App\Http\Requests\DeliveryGuy\ShowDeliveryGuyRequest;
use App\Http\Requests\DeliveryGuy\UpdateDeliveryGuyRequest;
use App\Repositories\DeliveryGuyRepository;
use App\Services\DeliveryGuyService;
use Illuminate\Http\Request;

class DeliveryGuyController extends BaseController
{
    public function __construct(
        private DeliveryGuyService $service,
        private DeliveryGuyRepository $repository,
    ) {}

    public function index(Request $request)
    {
        return $this->safeExecute(function () {
            $deliveriesGuys = $this->repository->getAllByEnterprise();

            return response()->json(['deliveriesGuys' => $deliveriesGuys], 200);
        }, 'Erro ao buscar entregadores', $request);
    }

    public function show(ShowDeliveryGuyRequest $request)
    {
        return $this->safeExecute(function () use ($request) {
            $deliveryGuy = $this->repository->findById($request->route('deliveryGuyID'));

            return response()->json(['deliveryGuy' => $deliveryGuy], 200);
        }, 'Erro ao buscar entregador', $request);
    }

    public function store(CreateDeliveryGuyRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            $this->service->create($request);
            $deliveryGuys = $this->repository->getAllByEnterprise();

            return response()->json(['deliveryGuys' => $deliveryGuys, 'message' => 'Entregador cadastrado'], 201);
        }, 'Erro ao criar entregador', $request);
    }

    public function update(UpdateDeliveryGuyRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            $this->service->update($request);
            $deliveriesGuys = $this->repository->getAllByEnterprise();

            return response()->json(['deliveriesGuys' => $deliveriesGuys, 'message' => 'Entregador atualizado'], 200);
        }, 'Erro ao atualizar entregador', $request);
    }

    public function destroy(DeleteDeliveryGuyRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            $this->repository->delete($request->route('deliveryGuyID'));

            $deliveriesGuys = $this->repository->getAllByEnterprise();

            return response()->json(['deliveriesGuys' => $deliveriesGuys, 'message' => 'Entregador deletado'], 201);
        }, 'Erro ao deletar entregador', $request);
    }
}
