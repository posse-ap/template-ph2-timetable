<?php

namespace App\Mail;

use App\Models\Announcement;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AnnouncementMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Announcement $announcement,
        public User $student
    ) {}

    public function envelope(): Envelope
    {
        $name = $this->announcement->course->name;

        return new Envelope(
            subject: "【{$name}】{$this->announcement->title}"
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.announcement'
        );
    }
}
