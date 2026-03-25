<?php

namespace App\Traits;

use App\Observers\CacheInvalidationObserver;

trait InvalidatesCache
{
    public static function bootInvalidatesCache(): void
    {
        static::observe(CacheInvalidationObserver::class);
    }

    public function getCacheTags(): array
    {
        return [];
    }
}
