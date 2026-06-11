<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Prediction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'match_game_id',
        'home_score',
        'away_score',
        'points',
        'locked_at',
    ];

    protected $casts = [
        'locked_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function matchGame(): BelongsTo
    {
        return $this->belongsTo(MatchGame::class);
    }

    public function calculatePoints(): int
    {
        $match = $this->matchGame;

        if (! $match->is_final || $match->home_score === null || $match->away_score === null) {
            return 0;
        }

        $points = config('company_contest.points');

        if ($this->home_score === $match->home_score && $this->away_score === $match->away_score) {
            return $points['exact_score'];
        }

        $actualDiff = $match->home_score - $match->away_score;
        $predictedDiff = $this->home_score - $this->away_score;

        if ($actualDiff === $predictedDiff) {
            return $points['goal_difference'];
        }

        if ($this->tendency($actualDiff) === $this->tendency($predictedDiff)) {
            return $points['tendency'];
        }

        return 0;
    }

    private function tendency(int $diff): int
    {
        return $diff <=> 0;
    }
}
