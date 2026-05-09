<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApprovalNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    protected $type;
    protected $status;
    protected $title;

    public function __construct(string $type, string $status, string $title)
    {
        $this->type = $type;
        $this->status = $status;
        $this->title = $title;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $statusText = $this->status === 'published' || $this->status === 'approved' ? 'Approved' : 'Rejected';
        return (new MailMessage)
            ->subject("Your {$this->type} has been {$statusText}")
            ->greeting("Hello {$notifiable->name},")
            ->line("The status of your {$this->type} \"{$this->title}\" has been updated to: " . strtoupper($this->status))
            ->action('View Dashboard', url('/dashboard'))
            ->line('Thank you for contributing to Nabha Digital Learning!');
    }

    public function toArray(object $notifiable): array
    {
        $statusText = $this->status === 'published' || $this->status === 'approved' ? 'Approved' : 'Rejected';
        return [
            'title'   => "{$this->type} {$statusText}",
            'message' => "Your {$this->type} \"{$this->title}\" has been {$this->status}.",
            'type'    => 'approval',
        ];
    }
}
