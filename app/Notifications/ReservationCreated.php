<?php

namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReservationCreated extends Notification
{
    public function __construct(public Reservation $reservation) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $r = $this->reservation;

        return (new MailMessage)
            ->subject('✅ Reserva recibida — ' . $r->space->name)
            ->greeting('¡Hola, ' . $r->user_name . '!')
            ->line('Hemos recibido tu solicitud de reserva para **' . $r->space->name . '**.')
            ->line('**Fecha y hora:** ' . $r->start_time->format('d/m/Y H:i') . ' – ' . $r->end_time->format('H:i'))
            ->line('**Ubicación:** ' . ($r->space->location ?? 'Por confirmar'))
            ->line('**Estado:** Pendiente de aprobación')
            ->when($r->total_price > 0, fn ($m) => $m->line('**Total:** $' . number_format($r->total_price, 2)))
            ->line('Este correo fue enviado automáticamente al registrar tu reserva.')
            ->line('Te notificaremos por correo si tu reserva cambia de estado.')
            ->action('Ver detalles', url('/reservations/' . $r->slug . '/confirmation'))
            ->salutation('Saludos del equipo de Gestión de Salas');
    }

    public function routeNotificationForMail(): string
    {
        return env('RESERVATION_NOTIFICATION_EMAIL', $this->reservation->user_email);
    }
}