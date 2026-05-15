<?php

namespace App\Services;

use App\DTO\DalleAdm\Commission\CreateCommissionDTO;
use App\Enums\Subscription\Subscription;
use App\Jobs\Notification\SendNotificationJob;
use App\Jobs\Payment\PaymentSuccessJob;
use App\Repositories\DalleAdm\CommissionRepository;
use App\Repositories\DalleAdm\SellerRepository;
use App\Repositories\EnterpriseRepository;
use App\Repositories\SubscriptionRepository;
use App\Repositories\UserRepository;

class WebhookAsaasService
{
    public function __construct(
        protected SubscriptionRepository $subscriptionRepository,
        protected UserRepository $userRepository,
        protected EnterpriseRepository $enterpriseRepository,
        protected SellerRepository $sellerAdmRepository,
        protected CommissionRepository $commissionAdmRepository,
    ) {}

    public function update($request): bool
    {
        $parsed = $this->parseExternalReference($request['payment']['externalReference']);

        $user = $this->userRepository->findByIdWithoutCache($parsed['userID']);

        $expiredDate = $this->calculateExpiredDate($parsed['monthQuantity']);

        $enterprise = $this->enterpriseRepository->findByIdWithoutCache($user->enterprise_id);

        if ($enterprise->first_payment_subscription === 0 && $enterprise->seller_id) {
            $this->createCommission($enterprise->seller_id, $enterprise, $request['payment']['value']);

            $updated = $this->enterpriseRepository->updateWithoutCache($user->enterprise_id, [
                'first_payment_subscription' => 1,
                'allow_test_free' => 0,
            ]);
        }

        $this->enterpriseRepository->updateWithoutCache($user->enterprise_id, [
            'subscription_id' => $parsed['subscriptionID'],
            'expired_date' => $expiredDate,
            'allow_test_free' => 0,
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
        return now()
            ->addMonths($monthQuantity)
            ->toDateTimeString();
    }

    private function sendRenewalNotification($user, int $subscriptionID, string $expiredDate): void
    {
        $subscription = $this->subscriptionRepository->findByIdWithoutCache($subscriptionID);
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

    private function createCommission(string $sellerID, $enterprise, float $totalValue)
    {
        $seller = $this->sellerAdmRepository->findByCode($sellerID);

        $commissionValue = $totalValue * ($seller->commission / 100);

        $commissionDTO = CreateCommissionDTO::fromRequest($seller, $enterprise, $commissionValue);

        return $this->commissionAdmRepository->create($commissionDTO->toArray());
    }
}
