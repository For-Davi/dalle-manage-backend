<?php

namespace App\Http\Controllers;

use App\Repositories\PermissionRepository;
use Illuminate\Http\Request;

class PermissionController extends BaseController
{
    public function __construct(private PermissionRepository $repository) {}

    public function index(Request $request)
    {
        return $this->safeExecute(function () {
            $permissions = $this->repository->getAll();

            return response()->json(['permissions' => $permissions], 200);
        }, 'Erro ao buscar permissões', $request);
    }
}
