<?php

namespace App\Console;

use App\Models\DealFollowUp;
use App\Jobs\SendDealFollowUpEmail;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // Follow-ups automáticos (a cada minuto)
        $schedule->call(function () {
            DealFollowUp::where('active', true)
                ->whereNotNull('next_send_at')
                ->where('next_send_at', '<=', now())
                ->get()
                ->each(function ($followUp) {
                    dispatch(new SendDealFollowUpEmail($followUp));
                });
        })->everyMinute()->withoutOverlapping();

        // Negócios inativos - Follow-ups (diário às 8h)
        $schedule->command('crm:detect-inactive-deals --days=5 --stage=follow_up')
            ->dailyAt('08:00')
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/inactive-deals.log'));

        // Negócios inativos - Leads (diário às 9h)
        $schedule->command('crm:detect-inactive-deals --days=3 --stage=lead')
            ->dailyAt('09:00')
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/inactive-leads.log'));

        // Negócios inativos - Negociação (diário às 10h)
        $schedule->command('crm:detect-inactive-deals --days=2 --stage=negotiation')
            ->dailyAt('10:00')
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/inactive-negotiation.log'));
    }

    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');
    }
}
