<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;

class CacheService
{
    public function __construct() {}

    public function clearCache(?int $enterpriseID = null): bool
    {
        $enterpriseID ??= Auth::user()?->enterprise_id;

        if (! $enterpriseID) {
            return false;
        }

        $pattern = "*enterprise:{$enterpriseID}:*";

        $redis = Redis::connection('cache');

        $keys = $redis->keys($pattern);

        if (! empty($keys)) {
            $redis->del($keys);
        }

        return true;
    }
}
