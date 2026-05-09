<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OtpNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    protected $otp;

    public function __construct($otp)
    {
        $this->otp = $otp;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Login OTP')
            ->greeting("Hello {$notifiable->name},")
            ->line("Your one-time password (OTP) for logging into Nabha Digital Learning is:")
            ->line($this->otp)
            ->line('This OTP is valid for 10 minutes.')
            ->line('If you did not request this, please ignore this email.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title'   => 'Login OTP Requested',
            'message' => "Your login OTP is {$this->otp}.",
            'type'    => 'otp',
        ];
    }
}
