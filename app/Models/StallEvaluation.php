<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StallEvaluation extends Model
{
    protected $fillable = [
        'student_id',
        'stall_id',
        'cleanliness',
        'service',
        'taste',
        'price',
        'comment',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function stall(): BelongsTo
    {
        return $this->belongsTo(Stall::class);
    }
}
