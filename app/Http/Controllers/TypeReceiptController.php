<?php

namespace App\Http\Controllers;

use App\Http\Requests\Receipt\Type\CreateTypeReceiptRequest;
use App\Http\Requests\Receipt\Type\DeleteTypeReceiptRequest;
use App\Http\Requests\Receipt\Type\UpdateTypeReceiptRequest;
use App\Repositories\TypeReceiptRepository;
use App\Services\TypeReceiptService;
use App\Utils\ErrorLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TypeReceiptController
{
    public function __construct(
        private TypeReceiptService $service,
        private TypeReceiptRepository $repository
    ) {}

    public function index(Request $request)
    {
        try {
            $types = $this->repository->getAllByEnterprise();

            return response()->json(['types' => $types], 200);
        } catch (\Exception $e) {
            ErrorLogger::log('Erro ao buscar os tipos de recebimentos', $e, $request);

            return response()->json(['message' => 'Erro ao buscar os tipos de recebimentos'], 500);
        }
    }

    public function store(CreateTypeReceiptRequest $request)
    {
        try {
            DB::beginTransaction();
            $type = $this->service->create($request);

            if ($type) {
                DB::commit();

                $types = $this->repository->getAllByEnterprise();

                return response()->json(['types' => $types, 'message' => 'Tipo de recebimento cadastrado'], 201);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            ErrorLogger::log('Erro ao cadastrar tipo:', $e, $request);

            return response()->json(['message' => 'Erro ao cadastrar tipo'], 500);
        }
    }

    public function update(UpdateTypeReceiptRequest $request)
    {
        try {
            DB::beginTransaction();
            $type = $this->service->update($request);

            if ($type) {
                DB::commit();

                $types = $this->repository->getAllByEnterprise();

                return response()->json(['types' => $types, 'message' => 'Tipo de recebimento atualizado'], 200);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            ErrorLogger::log('Erro ao atualizar categoria:', $e, $request);

            return response()->json(['message' => 'Erro ao atualizar tipo'], 500);
        }
    }

    public function destroy(DeleteTypeReceiptRequest $request)
    {
        try {
            DB::beginTransaction();

            $type = $this->repository->delete($request->route('typeID'));

            if ($type) {
                DB::commit();
                $types = $this->repository->getAllByEnterprise();

                return response()->json(['types' => $types, 'message' => 'Tipo de recebimento excluído'], 200);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            ErrorLogger::log('Erro ao excluir tipo de recebimento:', $e, $request);

            return response()->json(['message' => 'Erro ao excluir o tipo'], 500);
        }
    }
}
