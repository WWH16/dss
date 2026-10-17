<?php

namespace App\Mail;

use Carbon\CarbonInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

// ADDED: notifies an account after an admin changes its role. Modelled on PasswordChangedMail.
class RoleChangedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $name,
        public string $oldRoleLabel,
        public string $newRoleLabel,
        public CarbonInterface $changedAt,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your ISU Canteen Evaluation System role was changed',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.role-changed',
            with: [
                'name' => $this->name,
                'oldRoleLabel' => $this->oldRoleLabel,
                'newRoleLabel' => $this->newRoleLabel,
                'changedAtLabel' => $this->changedAt->copy()->timezone('Asia/Manila')->format('M j, Y, g:i A'),
            ],
        );
    }
}
