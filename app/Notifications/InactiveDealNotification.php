<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InactiveDealNotification extends Notification
{
   public function __construct(public $deal) {}

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'deal_id' => $this->deal->id,
            'title' => $this->deal->title,
            'message' => 'Negócio inativo há ' . $this->deal->inactive_days . ' dias.',
        ];
    }
}
