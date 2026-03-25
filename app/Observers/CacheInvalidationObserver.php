<?php

namespace App\Observers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redis;

class CacheInvalidationObserver
{
    public function created(Model $model): void
    {
        $this->clearCache($model);
    }

    public function updated(Model $model): void
    {
        $this->clearCache($model);
    }

    public function deleted(Model $model): void
    {
        $this->clearCache($model);
    }

    public function restored(Model $model): void
    {
        $this->clearCache($model);
    }

    private function clearCache(Model $model): void
    {
        $tags = $model->getCacheTags();

        if (empty($tags)) {
            return;
        }

        $redis = Redis::connection('cache');
        $cachePrefix = config('cache.prefix').':';

        foreach ($tags as $tag) {
            $indexKey = 'cache_index:'.$tag;

            $keys = $redis->smembers($indexKey);

            if (! empty($keys)) {
                $redis->pipeline(function ($pipe) use ($keys, $cachePrefix, $indexKey) {
                    foreach ($keys as $key) {
                        $pipe->unlink($cachePrefix.$key);
                    }
                    $pipe->del($indexKey);
                });
            } else {
                $redis->del($indexKey);
            }
        }
    }
}
