<?php

namespace App\Models;

use Illuminate\Auth\Notifications\ResetPassword; // ADDED
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;
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
        // CHANGED: removed course, track, year_level, student_number and stall_id; they live on the students and staff tables.
        'email_verified_at',
    ];

    // CHANGED: removed the PROGRAMS list; programs and their tracks are the programs and tracks tables.

    // CHANGED: removed the unused stall() relation; queries join stalls directly.

    // ADDED: each role keeps its own fields in its own table, keyed by user_id.
    public const PROFILE_TABLES = [
        'student' => 'students',
        'staff'   => 'staff',
        'admin'   => 'admins',
        'clinic'  => 'clinics',
    ];

    // ADDED: every user gets the row for its role on create, and the row moves when the role changes.
    protected static function booted(): void
    {
        static::created(function (User $user) {
            $user->insertProfileRow($user->role);
        });

        static::updated(function (User $user) {
            if ($user->wasChanged('role')) {
                if ($table = self::PROFILE_TABLES[$user->getOriginal('role')] ?? null) {
                    DB::table($table)->where('user_id', $user->id)->delete();
                }
                $user->insertProfileRow($user->role);
            }
        });
    }

    private function insertProfileRow(?string $role): void
    {
        if ($table = self::PROFILE_TABLES[$role] ?? null) {
            DB::table($table)->insert(['user_id' => $this->id, 'created_at' => now(), 'updated_at' => now()]);
        }
    }

    // ADDED: creates the user and fills its role row in one transaction.
    public static function createWithProfile(array $attributes, array $profile = []): static
    {
        return DB::transaction(function () use ($attributes, $profile) {
            $user = static::create($attributes);
            if ($profile) {
                DB::table(self::PROFILE_TABLES[$user->role])->where('user_id', $user->id)->update($profile + ['updated_at' => now()]);
            }

            return $user;
        });
    }

    // ADDED: the student's row (student number, program, track, year level).
    public function student(): HasOne
    {
        return $this->hasOne(Student::class);
    }

    // ADDED: the staff member's row (assigned stall).
    public function staff(): HasOne
    {
        return $this->hasOne(Staff::class);
    }

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

    // ADDED: Laravel's reset email, greeting the user by name instead of "Hello!". The ISU seal in the
    // mail header is attached for every email in AppServiceProvider.
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new class($token) extends ResetPassword {
            public function toMail($notifiable)
            {
                return parent::toMail($notifiable)->greeting("Hello, {$notifiable->name}!");
            }
        });
    }
}
