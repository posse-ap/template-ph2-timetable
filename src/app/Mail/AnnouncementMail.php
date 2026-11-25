<?php

namespace App\Mail;

use App\Models\Announcement;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class AnnouncementMail extends Mailable
{
    public function __construct(public Announcement $announcement)
    {
    }

    public function envelope(): Envelope
    {
        // 件名：【授業名】タイトル
        return new Envelope(
            subject: '【' . $this->announcement->course->name . '】' . $this->announcement->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.announcement',
        );
    }
}
