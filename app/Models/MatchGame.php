<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MatchGame extends Model
{
    use HasFactory;

    public const ROUND_GROUP = 'group';
    public const ROUND_FINAL = 'final';
    public const GROUP_STAGE_STAGES = ['Gruppenphase', 'Vorrunde', 'Group', '1. Runde', '2. Runde', '3. Runde'];

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

    public function roundType(): string
    {
        $stage = mb_strtolower($this->stage);
        $group = mb_strtolower((string) $this->group_name);

        if (str_contains($stage, 'gruppenphase')
            || str_contains($stage, 'vorrunde')
            || $stage === 'group'
            || preg_match('/^\d+\.\s*runde$/u', $stage)
            || str_contains($group, 'gruppe')
            || str_contains($group, 'group')) {
            return self::ROUND_GROUP;
        }

        return self::ROUND_FINAL;
    }

    public function isFinalRound(): bool
    {
        return $this->roundType() === self::ROUND_FINAL;
    }
}
