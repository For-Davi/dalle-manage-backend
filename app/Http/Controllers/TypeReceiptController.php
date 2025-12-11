<?php

namespace App\Http\Controllers;

use App\DTO\Receipt\FilterReceiptDTO;
use App\Http\Requests\Receipt\FilterReceiptRequest;
use App\Repositories\TypeReceiptRepository;
use App\Utils\ErrorLogger;
use Illuminate\Http\Request;

class TypeReceiptController
{
    public function __construct(
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

    public function filter(FilterReceiptRequest $request)
    {
        try {
            $typeReceiptFilterDTO = FilterReceiptDTO::fromRequest($request);
            $types = $this->repository->getAllWithFilter($typeReceiptFilterDTO);

            return response()->json(['types' => $types], 200);
        } catch (\Exception $e) {
            ErrorLogger::log('Erro ao buscar os tipos de recebimentos', $e, $request);

            return response()->json(['message' => 'Erro ao buscar os tipos de recebimentos'], 500);
        }
    }
}
