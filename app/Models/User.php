<?php

namespace App\Models;

use Illuminate\Auth\Notifications\ResetPassword; // ADDED
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'role',
        'name',
        'email',
        'password',
        'course',
        // CHANGED: added track, the specialization track within the course.
        'track',
        'year_level',
        'student_number',
        'stall_id',
        'email_verified_at',
    ];

    // CHANGED: the programs a student can pick, each with its specialization tracks (code => name). Registration, the profile form and the admin Students page all read this list.
    public const PROGRAMS = [
        'BSIT' => [
            'BA'   => 'Business Analytics',
            'WMAD' => 'Web and Mobile Application Development',
            'NS'   => 'Network Security',
            'BPO'  => 'Business Process Outsourcing',
        ],
        'BSCS' => [
            'BA' => 'Business Analytics',
            'DM' => 'Data Mining',
        ],
        'BSEMC' => [
            'GD' => 'Game Development',
            'DA' => 'Digital Animation',
        ],
    ];

    // CHANGED: removed the unused stall() relation; queries join stalls directly.

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // ADDED: Laravel's reset email, greeting the user by name instead of "Hello!" and carrying the ISU seal
    // as an embedded image for the mail header.
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new class($token) extends ResetPassword {
            public function toMail($notifiable)
            {
                return parent::toMail($notifiable)
                    ->greeting("Hello, {$notifiable->name}!")
                    ->withSymfonyMessage(fn ($message) => $message->embedFromPath(public_path('assets/images/isu_logo_email.png'), 'isu-logo'));
            }
        });
    }
}
