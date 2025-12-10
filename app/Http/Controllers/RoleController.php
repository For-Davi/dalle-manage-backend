<?php

namespace App\Http\Controllers;

use App\Http\Resources\Role\RoleSelectResource;
use App\Repositories\RoleRepository;
use App\Utils\ErrorLogger;
use Illuminate\Http\Request;

class RoleController
{
    public function __construct(private RoleRepository $repository) {}

    public function indexSelect(Request $request)
    {
        try {
            $roles = $this->repository->getAllByEnterprise();

            return response()->json(['roles' => RoleSelectResource::collection($roles)], 200);
        } catch (\Exception $e) {
            ErrorLogger::log('Erro ao buscar roles:', $e, $request);

            return response()->json(['message' => 'Erro ao buscar roles'], 500);
        }
    }

    // public function store(CreateDepartmentRequest $request)
    // {
    //     try {
    //         DB::beginTransaction();
    //         $department = $this->service->create($request);

    //         if ($department) {
    //             DB::commit();

    //             return response()->json(['message' => 'Departamento cadastrado'], 201);
    //         }
    //     } catch (\Exception $e) {
    //         DB::rollBack();

    //         ErrorLogger::log('Erro ao cadastrar departamento:', $e, $request);

    //         return response()->json(['message' => 'Erro ao cadastrar departamento'], 500);
    //     }
    // }

    // public function update(UpdateDepartmentRequest $request)
    // {
    //     try {
    //         DB::beginTransaction();
    //         $department = $this->service->update($request);

    //         if ($department) {
    //             DB::commit();

    //             return response()->json(['message' => 'Departamento atualizado'], 200);
    //         }
    //     } catch (\Exception $e) {
    //         DB::rollBack();

    //         ErrorLogger::log('Erro ao atualizar departamento:', $e, $request);

    //         return response()->json(['message' => 'Erro ao atualizar departamento'], 500);
    //     }
    // }

    // public function destroy(DeleteDepartmentRequest $request)
    // {
    //     try {
    //         DB::beginTransaction();

    //         $department = $this->repository->delete($request->route('id'));

    //         if ($department) {
    //             DB::commit();
    //             $departments = $this->repository->getAllByEnterprise($request->get('enterprise_id'));

    //             return response()->json(['departments' => $departments, 'message' => 'Departamento excluído'], 200);
    //         }
    //     } catch (\Exception $e) {
    //         DB::rollBack();

    //         ErrorLogger::log('Erro ao excluir departamento:', $e, $request);

    //         return response()->json(['message' => 'Erro ao excluir departamento'], 500);
    //     }
    // }
}
