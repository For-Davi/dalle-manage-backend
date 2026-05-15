<?php

namespace App\Console\Commands;

use App\Helpers\NotificationHelper;
use App\Models\Enterprise;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class CheckSubscriptionExpiredDate extends Command
{
    protected $signature = 'subscription:check-expired-date';

    protected $description = 'Verifica assinaturas próximas do vencimento e encerradas';

    public function handle(): void
    {
        $today = Carbon::today();

        $enterprises = Enterprise::with('users')
            ->whereNotNull('expired_date')
            ->get();

        $subscriptionFree = DB::table('subscriptions')
            ->where('name', 'free')
            ->first();

        foreach ($enterprises as $enterprise) {
            $expiredDate = Carbon::parse($enterprise->expired_date)->startOfDay();
            $daysRemaining = $today->diffInDays($expiredDate, false);

            if ($daysRemaining < 0) {
                $enterprise->update(['subscription_id' => $subscriptionFree->id, 'expired_date' => null]);

                $title = 'Assinatura encerrada';
                $message = 'Sua assinatura foi encerrada e seu plano retornou ao gratuito. '
                    .'Renove agora para continuar aproveitando todos os recursos do sistema.';

                $this->notifyUsers($enterprise, $title, $message);
                $this->info("Empresa [{$enterprise->id}] encerrada → plano free.");

                continue;
            }

            if ($daysRemaining == 0) {
                $title = 'Sua assinatura encerra hoje!';
                $message = 'Sua assinatura expira hoje. Renove agora e não perca o acesso '
                    .'aos recursos premium do sistema.';

                $this->notifyUsers($enterprise, $title, $message);
                $this->info("Empresa [{$enterprise->id}] vence hoje.");

                continue;
            }

            if (in_array($daysRemaining, [1, 2, 3])) {
                $dias = $daysRemaining === 1 ? 'apenas 1 dia' : "apenas {$daysRemaining} dias";

                $title = 'Sua assinatura está prestes a vencer';
                $message = "Sua assinatura expira em {$dias}. Realize a renovação agora "
                    .'para continuar aproveitando todos os benefícios do sistema sem interrupção.';

                $this->notifyUsers($enterprise, $title, $message);
                $this->info("Empresa [{$enterprise->id}] vence em {$daysRemaining} dia(s).");
            }
        }

        $this->info('Verificação de assinaturas concluída.');
    }

    private function notifyUsers(Enterprise $enterprise, string $title, string $message): void
    {
        foreach ($enterprise->users as $user) {
            NotificationHelper::create($user->id, $title, $message, $enterprise->id);
        }
    }
}
