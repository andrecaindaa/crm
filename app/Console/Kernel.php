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
        $schedule->call(function () {
            DealFollowUp::where('active', true)
                ->whereNotNull('next_send_at')
                ->where('next_send_at', '<=', now())
                ->get()
                ->each(function ($followUp) {
                    dispatch(new SendDealFollowUpEmail($followUp));
                });
        })->everyMinute();
    }

    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');
    }
}
