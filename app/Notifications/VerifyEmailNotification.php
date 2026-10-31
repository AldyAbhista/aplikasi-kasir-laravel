<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class VerifyEmailNotification extends Notification
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );

        return (new MailMessage)
            ->subject('Verifikasi Alamat Email Akun Kasir Anda')
            ->greeting('Halo, ' . $notifiable->name . '!')
            ->line('Terima kasih telah mendaftar di Aplikasi Kasir. Silakan klik tombol di bawah ini untuk memverifikasi alamat email Anda.')
            ->action('Verifikasi Email Saya', $verificationUrl)
            ->line('Jika Anda tidak merasa membuat akun ini, silakan abaikan pesan email ini.')
            ->salutation('Salam hormat, Tim Aplikasi Kasir');
    }
}
