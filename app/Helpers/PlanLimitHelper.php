<?php

namespace App\Helpers;

use App\Models\Enterprise;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PlanLimitHelper
{
    public static function checkPlan(string $resourceKey)
    {
        $enterprise = Enterprise::with('subscription')->find(Auth::user()->enterprise_id);

        $config = DB::table('plan_limits')
            ->where('subscription', $enterprise->subscription->name)
            ->where('resource_key', $resourceKey)
            ->first();

        if ($resourceKey === 'supplier_orders' && $enterprise->subscription->name !== 'advanced') {
            throw ValidationException::withMessages([
                'plan_limit' => ['Acesso negado! Para realizar pedidos de fornecedor você deve atualizar sua assinatura para a assinatura Profissional.'],
            ]);
        }

        if ($config->limit_value !== null) {
            $currentCount = DB::table($resourceKey)->where('enterprise_id', $enterprise->id)->count();

            if ($resourceKey === 'product_variants' && $config->subscription === 'premium' && $config->limit_value === $currentCount) {
                throw ValidationException::withMessages([
                    'plan_limit' => ['Você ja atingiu o limite de 10.000 produtos cadastrados!.'],
                ]);
            }
            if ($config->limit_value === $currentCount || $config->limit_value < $currentCount) {
                throw ValidationException::withMessages([
                    'plan_limit' => ['Acesso negado! Para realizar mais cadastros você deve atualizar sua assinatura.'],
                ]);
            }
        }
    }
}
