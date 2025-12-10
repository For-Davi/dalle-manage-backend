<?php

namespace App\Services;

use App\Enums\SubscriptionName;
use App\Jobs\PaymentMadeJob;
use App\Notification\SendNotification;
use App\Repositories\EnterpriseRepository;
use App\Repositories\SubscriptionRepository;
use App\Repositories\UserRepository;
use Carbon\Carbon;

class WebhookAsaasService
{
    public function __construct(
        protected SubscriptionRepository $subscriptionRepository,
        protected UserRepository $userRepository,
        protected EnterpriseRepository $enterpriseRepository,
        protected SendNotification $notification
    ) {}

    public function update($request)
    {
        $parts = explode('|', $request['payment']['externalReference']);
        $projectName = $parts[0];
        $userPart = $parts[1];
        $subscriptionPart = $parts[2];
        $monthQuantityPart = $parts[3];

        $userID = (int) str_replace('user_', '', $userPart);
        $subscriptionID = (int) str_replace('subscription_', '', $subscriptionPart);
        $monthQuantity = (int) str_replace('month_qnty_', '', $monthQuantityPart);

        $user = $this->userRepository->findById($userID);

        $expiredDate = Carbon::now('America/Sao_Paulo')->addMonths($monthQuantity)->format('Y-m-d H:i:s');

        $this->enterpriseRepository->update($user->enterprise_id, [
            'subscription_id' => $subscriptionID,
            'expired_date' => $expiredDate,
        ]);

        PaymentMadeJob::dispatch();

        \Log::info('passou por aqui');

        $subscription = $this->subscriptionRepository->findById($subscriptionID);
        $subscriptionName = SubscriptionName::from($subscription->name)->label();

        $this->notification->notifyAllUsersByEnterprise(
            $user->enterprise_id,
            '💲Assinatura Renovada',
            "O usuário **{$user->name}** renovou a assinatura com sucesso!
           Detalhes da Renovação:
           • **Plano:** {$subscriptionName}
           • **Novo Vencimento:** {$expiredDate}"
        );

        return true;
    }
}
