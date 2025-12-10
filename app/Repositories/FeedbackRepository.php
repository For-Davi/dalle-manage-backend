<?php

namespace App\Repositories;

use App\Models\Feedback;
use App\Repositories\Base\BaseRepository;

class FeedbackRepository extends BaseRepository
{
    public function __construct(Feedback $model)
    {
        parent::__construct($model);
    }
}
