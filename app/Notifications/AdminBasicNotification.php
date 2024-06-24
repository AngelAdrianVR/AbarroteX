<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminBasicNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public $title, public $description, public $url)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        if (app()->environment() == 'production') {
            return ['database', 'mail'];
        } else {
            return ['database', 'mail'];
        }
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->title)
            ->markdown('emails.basic-email-template', [
                'greeting' => '¡Hola!',
                'description' => $this->description,
                'url' => $this->url,
                'salutation' => 'No olvides revisarlo en el sistema. Saludos',
                'subcopy' => 'Si tienes alguna duda, dirígete a <a href="https://ezyventas.com/support/faqs">Soporte</a>, directamente desde el sistema. Si ya no deseas recibir nuestros correos electrónicos, <a href="https://ezyventas.com/support/faqs">cancela tu suscripción</a>.'
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'url' => $this->url
        ];
    }
}
