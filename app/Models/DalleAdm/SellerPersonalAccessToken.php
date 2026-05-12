<?php

// app/Models/DalleAdm/SellerPersonalAccessToken.php

namespace App\Models\DalleAdm;

use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;

class SellerPersonalAccessToken extends SanctumPersonalAccessToken
{
    protected $connection = 'dalle_manage_adm';

    protected $table = 'personal_access_tokens';
}
