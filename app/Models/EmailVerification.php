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

    /**
     * Check if this OTP is still valid (not expired, not used).
     */
    public function isValid(): bool
    {
        return !$this->used && $this->expires_at->isFuture();
    }
}
