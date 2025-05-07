<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BaseMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    protected $subjectLine;
    protected $viewTemplate;

    public function __construct($data, $subjectLine, $viewTemplate)
    {
        // Constructor accepts data, subject, and view path
        $this->data = $data;
        $this->subjectLine = $subjectLine;
        $this->viewTemplate = $viewTemplate;
    }

    public function envelope(): Envelope
    {
        // Set the email subject
        return new Envelope(
            subject: $this->subjectLine,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: $this->viewTemplate
        );
    }

    public function build()
    {
        return $this->view($this->viewTemplate)->with($this->data);
    }

    public function attachments(): array
    {
        return [];
    }
}
