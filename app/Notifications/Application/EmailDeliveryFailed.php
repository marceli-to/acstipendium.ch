<?php

namespace App\Notifications\Application;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmailDeliveryFailed extends Notification
{
    use Queueable;

    protected $data;

    /**
     * Create a new notification instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->from(env('MAIL_FROM_ADDRESS'))
            ->subject('Bestätigungs-E-Mail konnte nicht zugestellt werden')
            ->line('Die Bestätigungs-E-Mail für folgende Bewerbung konnte nicht zugestellt werden:')
            ->line('')
            ->line('**Bewerber:** '.$this->data['user_name'])
            ->line('**E-Mail-Adresse:** '.$this->data['recipient_email'])
            ->line('')
            ->line('**Fehler:** '.$this->data['error_message'])
            ->line('')
            ->line('Die Bewerbung wurde trotzdem erfolgreich gespeichert. Bitte kontaktieren Sie den Bewerber auf einem anderen Weg, um die Bewerbung zu bestätigen.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
