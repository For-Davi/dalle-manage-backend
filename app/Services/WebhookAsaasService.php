<?php

namespace App\Services;

use App\Enums\Subscription\Subscription;
use App\Jobs\Notification\SendNotificationJob;
use App\Jobs\Payment\PaymentSuccessJob;
use App\Repositories\EnterpriseRepository;
use App\Repositories\SubscriptionRepository;
use App\Repositories\UserRepository;

class WebhookAsaasService
{
    public function __construct(
        protected SubscriptionRepository $subscriptionRepository,
        protected UserRepository $userRepository,
        protected EnterpriseRepository $enterpriseRepository,
    ) {}

    public function update(array $request): bool
    {
        $parsed = $this->parseExternalReference($request['payment']['externalReference']);

        $user = $this->userRepository->findById($parsed['userID']);
        $expiredDate = $this->calculateExpiredDate($parsed['monthQuantity']);

        $this->enterpriseRepository->update($user->enterprise_id, [
            'subscription_id' => $parsed['subscriptionID'],
            'expired_date' => $expiredDate,
        ]);

        PaymentSuccessJob::dispatch();

        $this->sendRenewalNotification($user, $parsed['subscriptionID'], $expiredDate);

        return true;
    }

    private function parseExternalReference(string $externalReference): array
    {
        [$project, $userPart, $subscriptionPart, $monthQuantityPart] = explode('|', $externalReference);

        return [
            'userID' => (int) str_replace('user_', '', $userPart),
            'subscriptionID' => (int) str_replace('subscription_', '', $subscriptionPart),
            'monthQuantity' => (int) str_replace('month_qnty_', '', $monthQuantityPart),
        ];
    }

    private function calculateExpiredDate(int $monthQuantity): string
    {
        return now('America/Sao_Paulo')
            ->addMonths($monthQuantity)
            ->toDateTimeString();
    }

    private function sendRenewalNotification(mixed $user, int $subscriptionID, string $expiredDate): void
    {
        $subscription = $this->subscriptionRepository->findById($subscriptionID);
        $subscriptionName = Subscription::from($subscription->name)->label();

        SendNotificationJob::dispatch(
            'Assinatura Renovada',
            $this->buildRenewalMessage($user->name, $subscriptionName, $expiredDate),
            null,
            $user->enterprise_id
        );
    }

    private function buildRenewalMessage(string $userName, string $subscriptionName, string $expiredDate): string
    {
        return <<<MSG
        O usuário **{$userName}** renovou a assinatura com sucesso!

        💲 **Detalhes da Renovação:**
        • **Plano:** {$subscriptionName}
        • **Novo Vencimento:** {$expiredDate}
        MSG;
    }
}
