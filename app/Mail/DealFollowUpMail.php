<?php

namespace App\Mail;

use App\Models\Deal;
use Illuminate\Mail\Mailable;

class DealFollowUpMail extends Mailable
{
    public Deal $deal;
    public string $body;

    public function __construct(Deal $deal, string $body)
    {
        $this->deal = $deal;
        $this->body = $body;
    }

    public function build()
    {
        return $this
            ->subject('Follow-up da proposta')
            ->view('emails.followup')
            ->with([
                'body' => $this->body,
                'deal' => $this->deal,
            ]);
    }
}
