<?php

namespace App\Services;

use App\Http\Client\PaymentsHttpClient;
use App\Repositories\SubscriptionRepository;

class PixService
{
    public function __construct(protected SubscriptionRepository $subscriptionRepository, protected PaymentsHttpClient $http) {}

    public function store($request)
    {
        $subscription = $this->subscriptionRepository->findById($request->subscriptionID);

        $data = [
            'value' => $subscription->price,
            'userID' => $request->user()->id,
            'subscriptionID' => $subscription->id,
            'monthQuantity' => 1,
            'identifier' => 'dalle_manage',
        ];

        return $this->http->request('post', 'payment/pix', $data);
    }
}
