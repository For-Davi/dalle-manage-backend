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
            $types = $this->repository->getAllByEnterprise($request->get('enterprise_id'));

            return response()->json(['types' => $types], 200);
        } catch (\Exception $e) {
            ErrorLogger::log('Erro ao buscar os tipos de recebimentos', $e, $request);

            return response()->json(['message' => 'Erro ao buscar os tipos de recebimentos'], 500);
        }
    }
}
