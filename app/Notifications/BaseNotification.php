<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class BaseNotification extends Notification
{
    use Queueable;

    protected $data;
    protected $options;

    public function __construct(array $data, array $options = null)
    {
        $this->data = $data;
        $this->options = $options;
    }

    public function via($notifiable)
    {
        if (isset($this->options['sendEmail']) && $this->options['sendEmail']) {
            return ['mail'];
        }

        return ['database'];
    }

    public function toArray($notifiable)
    {
        return $this->data;
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->view($this->options['emailTemplate'], $this->data)
            ->subject($this->data['title']);
    }

    public function toDatabase($notifiable)
    {
        return $this->data;
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage($this->data);
    }
}
