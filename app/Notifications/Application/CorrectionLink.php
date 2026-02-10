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
            ->subject('Korrektur Ihrer Bewerbung – AC-Stipendium / Correction de votre candidature')
            ->markdown('notifications.application.correction-link', ['url' => $url]);
    }

    public function toArray($notifiable)
    {
        return [];
    }
}
