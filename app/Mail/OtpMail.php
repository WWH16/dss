<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $otp,
        public string $studentName,
        public ?string $email = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your ISU Canteen Evaluation System Verification Code',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.otp',
            with: [
                'otp'         => $this->otp,
                'studentName' => $this->studentName,
                'email'       => $this->email,
            ],
        );
    }
}
