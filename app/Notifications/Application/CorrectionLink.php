<?php

namespace App\Notifications\Application;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CorrectionLink extends Notification
{
    use Queueable;

    protected $token;

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $url = config('app.url').'/korrektur?token='.$this->token;

        return (new MailMessage)
            ->from(env('MAIL_FROM_ADDRESS'))
            ->replyTo(env('MAIL_REPLY_TO_ADDRESS'))
            ->subject('Korrektur Ihrer Bewerbung – AC-Stipendium')
            ->greeting('Guten Tag')
            ->line('Klicken Sie auf den folgenden Link, um Ihre Bewerbung zu korrigieren:')
            ->action('Bewerbung korrigieren', $url)
            ->line('Dieser Link ist 48 Stunden gültig.')
            ->salutation('Freundliche Grüsse, AC-Stipendium');
    }

    public function toArray($notifiable)
    {
        return [];
    }
}
