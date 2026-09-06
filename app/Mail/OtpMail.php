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
        $baseUrl = config('app.url');
        if ((empty($baseUrl) || str_contains($baseUrl, 'localhost')) && request()->hasHeader('Host')) {
            $scheme = (request()->secure() || app()->environment('production')) ? 'https://' : 'http://';
            $baseUrl = $scheme . request()->header('Host');
        }
        $verificationUrl = rtrim($baseUrl, '/') . '/verify-otp' . (!empty($this->email) ? '?email=' . urlencode($this->email) . '&code=' . urlencode($this->otp) : '');

        return new Content(
            view: 'emails.otp',
            with: [
                'otp'             => $this->otp,
                'studentName'     => $this->studentName,
                'email'           => $this->email,
                'verificationUrl' => $verificationUrl,
            ],
        );
    }
}
