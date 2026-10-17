<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Program extends Model
{
    public function tracks(): HasMany
    {
        return $this->hasMany(Track::class)->orderBy('id');
    }
}
