<?php

use App\Helpers\PermissionHelper;

if (! function_exists('check_permission')) {
    function check_permission(...$slugs)
    {
        PermissionHelper::hasPermissions($slugs);
    }
}
