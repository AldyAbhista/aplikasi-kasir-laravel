<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    public string $token;

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $resetUrl = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('Permintaan Reset Password Akun Kasir')
            ->greeting('Halo, ' . $notifiable->name . '!')
            ->line('Anda menerima email ini karena kami menerima permintaan reset password untuk akun kasir Anda.')
            ->action('Reset Password Sekarang', $resetUrl)
            ->line('Tautan reset password ini hanya berlaku selama 60 menit.')
            ->line('Jika Anda tidak pernah meminta reset password, abaikan email ini dan akun Anda tetap aman.')
            ->salutation('Salam hormat, Tim Aplikasi Kasir');
    }
}
