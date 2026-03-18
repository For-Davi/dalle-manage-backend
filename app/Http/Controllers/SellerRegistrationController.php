<?php

namespace App\Http\Controllers;

use App\Http\Requests\SellerRegistration\CreateSellerRegistrationRequest;
use App\Services\SellerService;
use App\Utils\ErrorLogger;
use Illuminate\Support\Facades\DB;

class SellerRegistrationController
{
    public function __construct(
        protected SellerService $service
    ) {}

    public function store(CreateSellerRegistrationRequest $request)
    {
        try {
            DB::beginTransaction();
            $registration = $this->service->create($request);

            if ($registration) {
                DB::commit();

                return response()->json(['message' => 'Solicitação enviada'], 201);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            ErrorLogger::log('Erro ao realizar solicitação de  associado:', $e);

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
