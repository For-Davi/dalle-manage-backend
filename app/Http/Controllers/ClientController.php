<?php

namespace App\Http\Controllers;

use App\DTO\Client\FilterClientDTO;
use App\Http\Requests\Client\CreateClientRequest;
use App\Http\Requests\Client\DeleteClientRequest;
use App\Http\Requests\Client\FilterClientRequest;
use App\Http\Requests\Client\ShowClientRequest;
use App\Http\Requests\Client\UpdateClientRequest;
use App\Repositories\ClientRepository;
use App\Services\ClientService;
use App\Utils\ErrorLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientController
{
    public function __construct(
        private ClientService $service,
        private ClientRepository $repository
    ) {}

    public function index(Request $request)
    {
        try {
            $clients = $this->repository->getAllByEnterprise();

            return response()->json(['clients' => $clients], 200);
        } catch (\Exception $e) {
            ErrorLogger::log('Erro ao buscar clientes:', $e, $request);

            return response()->json(['message' => 'Erro ao buscar clientes'], 500);
        }
    }

    public function show(ShowClientRequest $request)
    {
        try {
            $client = $this->repository->findById($request->route('clientID'));

            return response()->json(['client' => $client], 200);

        } catch (\Exception $e) {
            ErrorLogger::log('Erro ao buscar cliente:', $e, $request);

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function filter(FilterClientRequest $request)
    {
        try {
            $clientFilterDTO = FilterClientDTO::fromRequest([
                ...$request->only(['name', 'email', 'cpf', 'cnpj', 'country', 'state', 'city']),
            ]);
            $clients = $this->repository->getAllWithFilter($clientFilterDTO);

            return response()->json(['clients' => $clients], 200);

        } catch (\Exception $e) {
            ErrorLogger::log('Erro ao filtrar clientes:', $e, $request);

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function store(CreateClientRequest $request)
    {
        try {
            DB::beginTransaction();
            $client = $this->service->create($request);
            if ($client) {
                DB::commit();

                $clients = $this->repository->getAllByEnterprise();

                return response()->json(['clients' => $clients, 'message' => 'Cliente cadastrado'], 201);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            ErrorLogger::log('Erro ao cadastrar cliente:', $e, $request);

            return response()->json(['message' => 'Erro ao cadastrar cliente'], 500);
        }
    }

    public function update(UpdateClientRequest $request)
    {
        try {
            DB::beginTransaction();
            $supplier = $this->service->update($request);

            if ($supplier) {
                DB::commit();

                $clients = $this->repository->getAllByEnterprise();

                return response()->json(['clients' => $clients, 'message' => 'Cliente atualizado'], 200);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            ErrorLogger::log('Erro ao atualizar cliente:', $e, $request);

            return response()->json(['message' => 'Erro ao atualizar fornecedor'], 500);
        }
    }

    public function destroy(DeleteClientRequest $request)
    {
        try {
            DB::beginTransaction();

            $client = $this->repository->delete($request->route('clientID'));

            if ($client) {
                DB::commit();
                $clients = $this->repository->getAllByEnterprise();

                return response()->json(['clients' => $clients, 'message' => 'Cliente excluído'], 200);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            ErrorLogger::log('Erro ao excluir cliente:', $e, $request);

            return response()->json(['message' => 'Erro ao excluir cliente'], 500);
        }
    }
}
