<?php

namespace App\Http\Controllers;

use App\Repositories\SubscriptionRepository;
use Illuminate\Http\Request;

class SubscriptionController extends BaseController
{
    public function __construct(private SubscriptionRepository $repository) {}

    public function index(Request $request)
    {
        return $this->safeExecute(function () {
            $subscriptions = $this->repository->getAll();

            return response()->json(['subscriptions' => $subscriptions], 200);
        }, 'Erro ao buscar assinaturas', $request);
    }
}
