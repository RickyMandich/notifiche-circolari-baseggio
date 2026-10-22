<?php

namespace App\Mail;

use App\Models\Circular;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewCircularNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Circular $circular)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nuova circolare: '.$this->circular->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.new-circular',
        );
    }
}
