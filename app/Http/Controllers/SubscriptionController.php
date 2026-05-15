<?php

namespace App\Http\Controllers;

use App\Repositories\SubscriptionRepository;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends BaseController
{
    public function __construct(private SubscriptionRepository $repository, private SubscriptionService $service) {}

    public function index(Request $request)
    {
        return $this->safeExecute(function () {
            $subscriptions = $this->repository->getAll();

            return response()->json(['subscriptions' => $subscriptions], 200);
        }, 'Erro ao buscar assinaturas', $request);
    }

    public function test(Request $request)
    {
        return $this->safeTransaction(function () {
            $this->service->startTestFree();

            $user = Auth::user();
            $user->load(['enterprise', 'enterprise.subscription', 'image', 'role.permissions']);
            if ($user->image) {
                $user->image->url = asset($user->image->url);
            }

            return response()->json(['message' => 'Período de teste gratuito ativado com sucesso com validade de 7 dias.', 'user' => $user], 200);
        }, 'Erro ao iniciar teste gratuito', $request);
    }
}
