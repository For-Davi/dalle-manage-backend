<?php

namespace App\Services;

use App\Http\Client\PaymentsHttpClient;
use App\Repositories\SubscriptionRepository;

class CreditCardService
{
    public function __construct(protected SubscriptionRepository $subscriptionRepository, protected PaymentsHttpClient $http) {}

    public function store($request)
    {
        $subscription = $this->subscriptionRepository->findById($request->subscriptionID);

        $data = [
            'userID' => auth()->user()->id,
            'subscriptionID' => $subscription->id,
            'monthQuantity' => 1,
            'value' => $subscription->price,
            'description' => null,
            'installmentCount' => null,
            'identifier' => 'dalle_manage',
            'creditCard' => $request->creditCard,
            'creditCardHolderInfo' => $request->creditCardHolderInfo,
        ];

        return $this->http->request('post', '/payment/credit-card/', $data);
    }
}
