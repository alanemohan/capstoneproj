<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ComplaintNotification extends Notification
{
    use Queueable;

    protected $studentName;
    protected $subject;

    public function __construct(string $studentName, string $subject)
    {
        $this->studentName = $studentName;
        $this->subject = $subject;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title'   => 'New Complaint Received',
            'message' => "Student {$this->studentName} submitted a complaint: \"{$this->subject}\".",
            'type'    => 'complaint',
        ];
    }
}
