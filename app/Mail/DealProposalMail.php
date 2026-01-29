<?php

namespace App\Mail;

use App\Models\DealProposal;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DealProposalMail extends Mailable
{
    use Queueable, SerializesModels;

    public DealProposal $proposal;
    public string $body;

    public function __construct(DealProposal $proposal, string $body)
    {
        $this->proposal = $proposal;
        $this->body = $body;
    }

    public function build()
    {
        return $this
            ->subject('Proposta Comercial')
            ->view('emails.deal-proposal')
            ->attach(
                storage_path('app/' . $this->proposal->file_path),
                [
                    'as' => $this->proposal->original_name,
                ]
            );
    }
}
