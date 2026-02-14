<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Deal;
use App\Models\DealActivity;
use Carbon\Carbon;
use App\Notifications\InactiveDealNotification;

class DetectInactiveDeals extends Command
{
    protected $signature = 'crm:detect-inactive-deals
                            {--days=5 : Número de dias para considerar inativo}
                            {--stage=follow_up : Estágio a verificar}
                            {--notify : Notificar owners}';

    protected $description = 'Detecta negócios sem atividade e cria alerta automático';

    public function handle()
    {
        $days = $this->option('days');
        $stage = $this->option('stage');
        $notify = $this->option('notify');

        $this->info("🔍 Verificando negócios no estágio '{$stage}' inativos há +{$days} dias...");

        $deals = Deal::with([
        'owner',
        'activities',
        'proposals',
        'followUps'
    ])
    ->where('stage', $stage)
    ->get();


        $count = 0;

        foreach ($deals as $deal) {
            $lastActivity = $deal->lastActivityAt();

            if (!$lastActivity) {
                continue;
            }

            $thresholdDate = now()->subDays($days)->startOfDay();

            if ($lastActivity->lt($thresholdDate))
            {

                // Verificar se já existe alerta recente (últimos 3 dias)
                $recentAlert = $deal->activities()
                    ->where('type', 'system_inactive')
                    ->where('created_at', '>=', now()->subDays(3))
                    ->exists();

                if ($recentAlert) {
                    $this->line("   ⏭️  Negócio #{$deal->id} já tem alerta recente");
                    continue;
                }

                // Criar atividade de inatividade
                DealActivity::create([
                    'deal_id' => $deal->id,
                    'user_id' => null,
                    'type' => 'system_inactive',
                    'label' => '🚨 Negócio sem atividade',
                    'description' => "Sem atividade há " . $lastActivity->diffInDays(now()) . " dias.",
                    'meta' => [
                        'last_activity' => $lastActivity->toDateTimeString(),
                        'inactive_days' => $lastActivity->diffInDays(now()),
                        'threshold_days' => $days,
                        'stage' => $stage,
                    ],
                    'created_at' => now(),
                ]);

                $this->line("   ✅ Alerta criado para negócio #{$deal->id}: {$deal->title}");
                $count++;

                // Notificar owner se solicitado
                if ($notify && $deal->owner) {
                    $deal->owner->notify(new InactiveDealNotification($deal));
                }

            }
        }

        $this->newLine();
        $this->info("✅ Verificação concluída. {$count} alertas criados.");

        return Command::SUCCESS;
    }
}
