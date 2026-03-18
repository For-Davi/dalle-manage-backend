<?php

namespace App\Http\Controllers;

use App\DTO\Receipt\FilterReceiptDTO;
use App\Http\Requests\Receipt\FilterReceiptRequest;
use App\Repositories\TypeReceiptRepository;
use Illuminate\Http\Request;

class TypeReceiptController extends BaseController
{
    public function __construct(
        private TypeReceiptRepository $repository
    ) {}

    public function index(Request $request)
    {
        return $this->safeExecute(function () {
            $types = $this->repository->getAllByEnterprise();

            return response()->json(['types' => $types], 200);
        }, 'Erro ao buscar os tipos de recebimentos', $request);
    }

    public function indexWithoutCredit(FilterReceiptRequest $request)
    {
        return $this->safeExecute(function () {
            $types = $this->repository->getAllWithoutCredit();

            return response()->json(['types' => $types], 200);
        }, 'Erro ao buscar os tipos de recebimentos', $request);
    }

    public function filter(FilterReceiptRequest $request)
    {
        return $this->safeExecute(function () use ($request) {
            $typeReceiptFilterDTO = FilterReceiptDTO::fromRequest($request);
            $types = $this->repository->getAllWithFilter($typeReceiptFilterDTO);

            return response()->json(['types' => $types], 200);
        }, 'Erro ao buscar os tipos de recebimentos', $request);
    }
}
