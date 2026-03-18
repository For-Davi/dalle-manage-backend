<?php

namespace App\Http\Controllers;

use App\Http\Resources\Role\RoleSelectResource;
use App\Repositories\RoleRepository;
use Illuminate\Http\Request;

class RoleController extends BaseController
{
    public function __construct(private RoleRepository $repository) {}

    public function indexSelect(Request $request)
    {
        return $this->safeExecute(function () {
            $roles = $this->repository->getAllByEnterprise();

            return response()->json(['roles' => RoleSelectResource::collection($roles)], 200);
        }, 'Erro ao buscar roles', $request);
    }
}
