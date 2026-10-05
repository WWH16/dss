<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailVerification extends Model
{
    protected $fillable = [
        'email',
        'otp',
        'payload',
        'expires_at',
        'used',
    ];

    protected $casts = [
        'payload'    => 'array',
        'expires_at' => 'datetime',
        'used'       => 'boolean',
    ];

    // CHANGED: removed the unused isValid() helper; OtpController checks expiry in its queries.
}
