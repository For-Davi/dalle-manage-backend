<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;

class CacheService
{
    public function __construct() {}

    public function clearCache()
    {
        $enterpriseID = Auth::user()->enterprise_id;
        $pattern = "*enterprise:{$enterpriseID}:*";

        $redis = Redis::connection('cache');

        $keys = $redis->keys($pattern);

        if (!empty($keys)) {
            $redis->del($keys);
        }

        return true;
    }
}
