<?php

namespace App\Models\DalleAdm;

use Illuminate\Database\Eloquent\Model;

class SellerRegistration extends Model
{
    protected $connection = 'dalle_manage_adm';

    protected $table = 'registrations';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'description',
    ];
}
