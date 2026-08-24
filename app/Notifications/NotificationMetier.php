<?php

namespace App\Notifications;

use App\Contracts\EvenementMetier;
use Illuminate\Notifications\Notification;

class NotificationMetier extends Notification
{
    protected array $payload;

    public function __construct(EvenementMetier $evenement)
    {
        $this->payload = $evenement->payload();
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return $this->payload;
    }
}
