<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RefundStatusNotification extends Notification
{
    use Queueable;

    protected string $courseTitle;
    protected string $status;
    protected float $amount;
    protected ?string $notes;

    public function __construct(string $courseTitle, string $status, float $amount, ?string $notes = null)
    {
        $this->courseTitle = $courseTitle;
        $this->status = $status;
        $this->amount = $amount;
        $this->notes = $notes;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $approved = in_array($this->status, ['partial', 'full'], true);
        $subject = $approved ? 'Refund Approved' : 'Refund Rejected';

        $mail = (new MailMessage)
            ->subject($subject . ': ' . $this->courseTitle)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line($approved
                ? 'Your refund request for "' . $this->courseTitle . '" has been approved.'
                : 'Your refund request for "' . $this->courseTitle . '" has been rejected.')
            ->line('Refund amount: ₹' . number_format($this->amount, 2));

        if ($this->notes) {
            $mail->line('Note: ' . $this->notes);
        }

        $mail->action('View My Courses', url('/student/courses/my-courses'));

        return $mail;
    }

    public function toArray(object $notifiable): array
    {
        $approved = in_array($this->status, ['partial', 'full'], true);

        return [
            'title' => ($approved ? 'Refund Approved' : 'Refund Rejected') . ' - ' . $this->courseTitle,
            'message' => $approved
                ? 'Your refund request for "' . $this->courseTitle . '" was approved for ₹' . number_format($this->amount, 2)
                : 'Your refund request for "' . $this->courseTitle . '" was rejected.',
            'type' => 'refund',
            'status' => $this->status,
            'amount' => $this->amount,
        ];
    }
}