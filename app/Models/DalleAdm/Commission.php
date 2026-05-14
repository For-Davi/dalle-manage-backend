<?php

namespace App\Models\DalleAdm;

use Illuminate\Database\Eloquent\Model;

class Commission extends Model
{
    protected $connection = 'dalle_manage_adm';

    protected $table = 'commissions';

    protected $fillable = [
        'enterprise_id',
        'enterprise_name',
        'enterprise_email',
        'seller_id',
        'seller_name',
        'seller_cpf',
        'seller_email',
        'percentage',
        'commission_value',
    ];
}
