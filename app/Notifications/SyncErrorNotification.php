<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use NotificationChannels\Pushover\PushoverChannel;
use NotificationChannels\Pushover\PushoverMessage;

class SyncErrorNotification extends Notification
{
    protected $message;

    public function __construct($message)
    {
        $this->message = $message;
    }

    public function via($notifiable): array
    {
        return [PushoverChannel::class];
    }

    public function toPushover($notifiable)
    {
        return PushoverMessage::create($this->message)
            ->title('Sync error')
            ->sound('incoming')
            ->highPriority()
            ->url('http://localhost/hub', 'Bekijk de hub');
    }

    public function toArray($notifiable): array
    {
        return [];
    }
}
