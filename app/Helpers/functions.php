<?php

use App\Helpers\PermissionHelper;
use App\Helpers\PlanLimitHelper;

if (! function_exists('check_permission')) {
    function check_permission(...$slugs)
    {
        Log::info(['slugs' => $slugs]);
        PermissionHelper::hasPermissions($slugs);
    }
}
if (! function_exists('check_plan')) {
    function check_plan(string $resourceKey)
    {
        Log::info(['plan_limit' => $resourceKey]);
        PlanLimitHelper::checkPlan($resourceKey);
    }
}
