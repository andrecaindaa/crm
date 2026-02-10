<?php

namespace App\Jobs;

use App\Models\DealFollowUp;
use App\Mail\DealFollowUpMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;


class SendDealFollowUpEmail implements ShouldQueue
{
    public function __construct(
        public DealFollowUp $followUp
    ) {}

    public function handle()
    {
        if (! $this->followUp->active) {
            return;
        }

        $deal = $this->followUp->deal;

        if ($deal->stage !== 'follow_up') {
            $this->followUp->update(['active' => false]);
            return;
        }

        $email =
            $deal->person?->email
            ?? $deal->entity?->email;

        if (! $email) {
            return;
        }

        $body = collect(config('followups.emails'))->random();

        Mail::to($email)->send(
            new DealFollowUpMail($deal, $body)
        );

        // agenda próximo envio (2 dias depois)
        $this->followUp->update([
            'sent_at' => now(),
            //'next_send_at' => now()->addDays(2),
                'next_send_at' => now()->addDays(
                 $this->followUp->interval_days
                ),

        ]);
    }
}
