<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentStatusNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    protected $courseTitle;
    protected $status;
    protected $amount;

    public function __construct(string $courseTitle, string $status, float $amount)
    {
        $this->courseTitle = $courseTitle;
        $this->status = $status;
        $this->amount = $amount;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $statusText = $this->status === 'paid' ? 'Successful' : 'Failed';
        $message = $this->status === 'paid' 
            ? "Your payment for \"{$this->courseTitle}\" was successful."
            : "Your payment for \"{$this->courseTitle}\" has failed.";

        $mail = (new MailMessage)
            ->subject("Payment {$statusText}: {$this->courseTitle}")
            ->greeting("Hello {$notifiable->name},")
            ->line($message)
            ->line("Amount: ₹" . number_format($this->amount, 2));

        if ($this->status === 'paid') {
            $mail->action('Start Learning', url('/student/courses/my-courses'));
        } else {
            $mail->action('Try Again', url('/student/cart'));
        }

        return $mail;
    }

    public function toArray(object $notifiable): array
    {
        $statusText = $this->status === 'paid' ? 'Successful' : 'Failed';
        return [
            'title'   => "Payment {$statusText}",
            'message' => "Your payment for \"{$this->courseTitle}\" was {$this->status}.",
            'type'    => 'payment',
            'amount'  => $this->amount,
        ];
    }
}
