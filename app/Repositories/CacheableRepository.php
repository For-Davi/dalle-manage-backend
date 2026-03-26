<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

trait CacheableRepository
{
    protected function remember(string $key, \Closure $callback, int $ttlHours = 6): mixed
    {
        return Cache::remember($key, now()->addHours($ttlHours), $callback);
    }

    protected function enterpriseCacheKey(string $prefix): string
    {
        return "{$prefix}:enterprise:".Auth::user()->enterprise_id;
    }
}
