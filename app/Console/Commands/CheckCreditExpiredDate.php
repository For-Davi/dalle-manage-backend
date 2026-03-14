<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckCreditExpiredDate extends Command
{
    protected $signature = 'check:clients-expired-date';

    protected $description = 'Faz uma verificação nos clientes que possuem data de expiração de crédito';

    public function handle()
    {
        DB::table('clients')->where('credit_expires_at', '<', now())->update([
            'credits' => 0,
            'credit_expires_at' => null,
        ]);

        $this->info('Verificação feita com sucesso!');
    }
}
