<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Deal;
use App\Models\DealActivity;
use Carbon\Carbon;

class DetectInactiveDeals extends Command
{
    protected $signature = 'crm:detect-inactive-deals';
    protected $description = 'Detecta negócios sem atividade e cria alerta automático';

    public function handle()
    {
        $days = 5; // podes tornar configurável depois

        $deals = Deal::where('stage', 'follow_up')->get();

        foreach ($deals as $deal) {

            $lastActivity = $deal->lastActivityAt();

            if (!$lastActivity) {
                continue;
            }

            if ($lastActivity->lt(now()->subDays($days))) {

                // evitar duplicar alerta
                $alreadyExists = $deal->activities()
                    ->where('type', 'system_inactive')
                    ->whereDate('created_at', today())
                    ->exists();

                if ($alreadyExists) {
                    continue;
                }

                DealActivity::create([
                    'deal_id' => $deal->id,
                    'user_id' => null,
                    'type' => 'system_inactive',
                    'label' => 'Negócio sem atividade',
                    'description' => "Sem atividade há mais de {$days} dias.",
                    'created_at' => now(),
                ]);
            }
        }

        $this->info('Verificação de negócios inativos concluída.');
    }
}
