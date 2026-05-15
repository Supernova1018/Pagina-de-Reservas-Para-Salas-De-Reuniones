<?php

namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReservationStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Reservation $reservation,
        public string $newStatus
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $r = $this->reservation;

        $config = match ($this->newStatus) {
            'confirmed' => [
                'icon'    => '🎉',
                'subject' => 'Reserva confirmada — ' . $r->space->name,
                'greeting'=> '¡Buenas noticias, ' . $r->user_name . '!',
                'line'    => 'Tu reserva para **' . $r->space->name . '** ha sido **confirmada**.',
                'extra'   => 'Recuerda presentarte a tiempo. Te esperamos.',
            ],
            'rejected' => [
                'icon'    => '❌',
                'subject' => 'Reserva rechazada — ' . $r->space->name,
                'greeting'=> 'Hola, ' . $r->user_name . '.',
                'line'    => 'Lamentamos informarte que tu reserva para **' . $r->space->name . '** ha sido **rechazada**.',
                'extra'   => 'Puedes intentar reservar en otro horario disponible.',
            ],
            'cancelled' => [
                'icon'    => '🚫',
                'subject' => 'Reserva cancelada — ' . $r->space->name,
                'greeting'=> 'Hola, ' . $r->user_name . '.',
                'line'    => 'Tu reserva para **' . $r->space->name . '** ha sido **cancelada**.',
                'extra'   => 'Si tienes preguntas, comunícate con nosotros.',
            ],
            default => [
                'icon'    => 'ℹ️',
                'subject' => 'Actualización de reserva — ' . $r->space->name,
                'greeting'=> 'Hola, ' . $r->user_name . '.',
                'line'    => 'El estado de tu reserva ha cambiado a **' . $r->status_label . '**.',
                'extra'   => '',
            ],
        };

        $mail = (new MailMessage)
            ->subject($config['icon'] . ' ' . $config['subject'])
            ->greeting($config['greeting'])
            ->line($config['line'])
            ->line('**Sala:** ' . $r->space->name)
            ->line('**Fecha:** ' . $r->start_time->format('d/m/Y H:i') . ' – ' . $r->end_time->format('H:i'));

        if ($config['extra']) {
            $mail->line($config['extra']);
        }

        return $mail
            ->action('Ver disponibilidad', url('/spaces/' . $r->space->slug))
            ->salutation('Equipo de Gestión de Salas');
    }

    public function routeNotificationForMail(): string
    {
        return $this->reservation->user_email;
    }
}