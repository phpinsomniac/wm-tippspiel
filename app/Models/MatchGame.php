<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MatchGame extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_name',
        'stage',
        'home_team',
        'away_team',
        'starts_at',
        'home_score',
        'away_score',
        'is_final',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'is_final' => 'boolean',
    ];

    public function predictions(): HasMany
    {
        return $this->hasMany(Prediction::class);
    }

    public function isPredictionOpen(): bool
    {
        return now()->lt($this->starts_at) && ! $this->is_final;
    }
}
