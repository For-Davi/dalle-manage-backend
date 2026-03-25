<?php

namespace App\Services;

use App\Enums\Subscription\Subscription;
use App\Jobs\Payment\PaymentMadeJob;
use App\Notification\SendNotification;
use App\Repositories\EnterpriseRepository;
use App\Repositories\SubscriptionRepository;
use App\Repositories\UserRepository;

class WebhookAsaasService
{
    public function __construct(
        protected SubscriptionRepository $subscriptionRepository,
        protected UserRepository $userRepository,
        protected EnterpriseRepository $enterpriseRepository,
        protected SendNotification $notification
    ) {}

    public function update($request): bool
    {
        [$project, $userPart, $subscriptionPart, $monthQuantityPart] = explode('|', $request['payment']['externalReference']);

        $userID = (int) str_replace('user_', '', $userPart);
        $subscriptionID = (int) str_replace('subscription_', '', $subscriptionPart);
        $monthQuantity = (int) str_replace('month_qnty_', '', $monthQuantityPart);

        $user = $this->userRepository->findById($userID);

        $expiredDate = now('America/Sao_Paulo')
            ->addMonths($monthQuantity)
            ->toDateTimeString();

        $this->enterpriseRepository->update($user->enterprise_id, [
            'subscription_id' => $subscriptionID,
            'expired_date' => $expiredDate,
        ]);

        PaymentMadeJob::dispatch();

        $subscription = $this->subscriptionRepository->findById($subscriptionID);
        $subscriptionName = Subscription::from($subscription->name)->label();

        $this->notification->notifyAllUsersByEnterprise(
            $user->enterprise_id,
            '💲 Assinatura Renovada',
            sprintf(
                "O usuário **%s** renovou a assinatura com sucesso!\n".
                "Detalhes da Renovação:\n".
                "• **Plano:** %s\n".
                '• **Novo Vencimento:** %s',
                $user->name,
                $subscriptionName,
                $expiredDate
            )
        );

        return true;
    }
}
