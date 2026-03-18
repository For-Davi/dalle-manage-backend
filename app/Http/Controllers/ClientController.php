<?php

namespace App\Http\Controllers;

use App\DTO\Client\FilterClientDTO;
use App\Http\Requests\Client\CreateClientRequest;
use App\Http\Requests\Client\DeleteClientRequest;
use App\Http\Requests\Client\FilterClientRequest;
use App\Http\Requests\Client\GetClientCreditRequest;
use App\Http\Requests\Client\ShowClientRequest;
use App\Http\Requests\Client\UpdateClientRequest;
use App\Repositories\ClientRepository;
use App\Services\ClientService;
use Illuminate\Http\Request;

class ClientController extends BaseController
{
    public function __construct(
        private ClientService $service,
        private ClientRepository $repository
    ) {}

    public function index(Request $request)
    {
        return $this->safeExecute(function () {
            $clients = $this->repository->getAllByEnterprise();

            return response()->json(['clients' => $clients], 200);
        }, 'Erro ao buscar clientes', $request);
    }

    public function show(ShowClientRequest $request)
    {
        return $this->safeExecute(function () use ($request) {
            $client = $this->repository->findById($request->route('clientID'));

            return response()->json(['client' => $client], 200);
        }, 'Erro ao buscar cliente', $request);
    }

    public function filter(FilterClientRequest $request)
    {
        return $this->safeExecute(function () use ($request) {
            $clientFilterDTO = FilterClientDTO::fromRequest($request);
            $clients = $this->repository->getAllWithFilter($clientFilterDTO);

            return response()->json(['clients' => $clients], 200);
        }, 'Erro ao filtrar clientes', $request);
    }

    public function store(CreateClientRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            $this->service->create($request);
            $clients = $this->repository->getAllByEnterprise();

            return response()->json(['clients' => $clients, 'message' => 'Cliente cadastrado'], 201);
        }, 'Erro ao cadastrar cliente', $request);
    }

    public function update(UpdateClientRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            $this->service->update($request);
            $clients = $this->repository->getAllByEnterprise();

            return response()->json(['clients' => $clients, 'message' => 'Cliente atualizado'], 200);
        }, 'Erro ao atualizar cliente', $request);
    }

    public function destroy(DeleteClientRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            $this->repository->delete($request->route('clientID'));
            $clients = $this->repository->getAllByEnterprise();

            return response()->json(['clients' => $clients, 'message' => 'Cliente excluído'], 200);
        }, 'Erro ao excluir cliente', $request);
    }

    public function getCredit(GetClientCreditRequest $request)
    {
        return $this->safeExecute(function () use ($request) {
            $credit = $this->service->getCredit($request);

            return response()->json(['credit' => $credit], 200);
        }, 'Erro ao buscar crédito do cliente', $request);
    }
}
