<?php

namespace App\Http\Controllers;

use App\Http\Requests\SellerRegistration\CreateSellerRegistrationRequest;
use App\Services\SellerService;

class SellerRegistrationController extends BaseController
{
    public function __construct(
        protected SellerService $service
    ) {}

    public function store(CreateSellerRegistrationRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            $this->service->create($request);

            return response()->json(['message' => 'Solicitação enviada'], 201);
        }, 'Erro ao realizar solicitação de associado', $request);
    }
}
