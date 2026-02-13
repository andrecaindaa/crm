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
    // Follow-ups automáticos
    $schedule->call(function () {
        DealFollowUp::where('active', true)
            ->whereNotNull('next_send_at')
            ->where('next_send_at', '<=', now())
            ->get()
            ->each(function ($followUp) {
                dispatch(new SendDealFollowUpEmail($followUp));
            });
    })->everyMinute();

    // Negócios inativos
    $schedule->command('crm:detect-inactive-deals')
        ->dailyAt('08:00');
}


    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');
    }

}
