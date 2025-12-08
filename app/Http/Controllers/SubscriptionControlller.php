<?php

namespace App\Http\Controllers;

use App\Repositories\SubscriptionRepository;
use App\Utils\ErrorLogger;
use Illuminate\Http\Request;

class SubscriptionControlller
{
    public function __construct(private SubscriptionRepository $repository) {}

    public function index(Request $request)
    {
        try {
            $subscriptions = $this->repository->getAll();

            return response()->json(['subscriptions' => $subscriptions], 200);
        } catch (\Exception $e) {
            ErrorLogger::log('Erro ao buscar assinaturas:', $e, $request);

            return response()->json(['message' => 'Erro ao buscar assinaturas'], 500);
        }
    }
}
