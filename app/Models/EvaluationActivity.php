<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EvaluationActivity extends Model
{
    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function stalls(): BelongsToMany
    {
        return $this->belongsToMany(Stall::class, 'evaluation_activity_stall');
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(StallEvaluation::class);
    }

    public function getStatusAttribute(): string
    {
        $today = now()->startOfDay();

        if (!$this->is_active) {
            return 'inactive';
        }

        if ($today->lt($this->start_date)) {
            return 'upcoming';
        }

        if ($today->gt($this->end_date)) {
            return 'ended';
        }

        return 'ongoing';
    }
}
