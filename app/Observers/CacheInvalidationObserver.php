<?php

namespace App\Observers;

use App\Services\CacheService;

class CacheInvalidationObserver
{
    public function __construct(protected CacheService $cacheService) {}

    public function created(): void
    {
        $this->cacheService->clearCache();
    }

    public function updated(): void
    {
        $this->cacheService->clearCache();
    }

    public function deleted(): void
    {
        $this->cacheService->clearCache();
    }

    public function restored(): void
    {
        $this->cacheService->clearCache();
    }
}
