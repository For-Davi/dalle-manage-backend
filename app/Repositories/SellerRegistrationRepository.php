<?php

namespace App\Repositories;

use App\Models\DalleAdm\SellerRegistration;
use App\Repositories\Base\BaseRepository;

class SellerRegistrationRepository extends BaseRepository
{
    public function __construct(SellerRegistration $model)
    {
        parent::__construct($model);
    }
}
