<?php

namespace App\Models\DalleAdm;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Seller extends Authenticatable
{
    use HasApiTokens;

    protected $connection = 'dalle_manage_adm';

    protected $table = 'sellers';

    protected $fillable = [
        'name',
        'email',
        'password',
    ];
}
