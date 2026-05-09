<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MentorAssignedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    protected $mentor;

    public function __construct($mentor)
    {
        $this->mentor = $mentor;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Mentor Assigned')
            ->greeting("Hello {$notifiable->name},")
            ->line("A new mentor has been assigned to help you with your learning journey.")
            ->line("Mentor Name: {$this->mentor->name}")
            ->line("Email: {$this->mentor->email}")
            ->action('View Mentor Details', url('/student/profile'))
            ->line('You can now reach out to your mentor for guidance.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title'   => 'Mentor Assigned',
            'message' => "{$this->mentor->name} has been assigned as your mentor.",
            'type'    => 'mentor',
        ];
    }
}
