<?php

namespace App\Mail;

use Carbon\CarbonInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

// ADDED: notifies a student after their password is changed. Modelled on OtpMail.
class PasswordChangedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $studentName,
        public CarbonInterface $changedAt,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your ISU Canteen Evaluation System password was changed',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.password-changed',
            with: [
                'studentName' => $this->studentName,
                'changedAtLabel' => $this->changedAt->copy()->timezone('Asia/Manila')->format('M j, Y, g:i A'),
            ],
        );
    }
}
