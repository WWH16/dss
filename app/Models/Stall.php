<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Stall extends Model
{
    protected $fillable = [
        'name',
        'is_active',
        'description',
        'staff_id',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function staff(): HasMany
    {
        return $this->hasMany(User::class, 'stall_id')->where('role', 'staff');
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(StallEvaluation::class);
    }

    public function activities(): BelongsToMany
    {
        return $this->belongsToMany(EvaluationActivity::class, 'evaluation_activity_stall');
    }
}
