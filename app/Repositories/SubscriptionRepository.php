<?php

namespace App\Repositories;

use App\Models\Subscription;

class SubscriptionRepository
{
    public function __construct(protected Subscription $model) {}

    public function getAll()
    {
        return $this->model->get();
    }

    public function findById($id)
    {
        return $this->model->find($id);
    }
}
