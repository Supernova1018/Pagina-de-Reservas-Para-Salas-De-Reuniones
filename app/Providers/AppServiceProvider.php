<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ResetPassword::toMailUsing(function (object $notifiable, string $url) {
            return (new MailMessage)
                ->subject('Restablecer contraseña - Reservas Salas')
                ->greeting('Hola ' . $notifiable->name . ',')
                ->line('Recibimos una solicitud para restablecer tu contraseña en Reservas Salas.')
                ->action('Restablecer contraseña', $url)
                ->line('Si no solicitaste este cambio, puedes ignorar este correo.')
                ->salutation('Equipo de Reservas Salas');
        });
    }
}
