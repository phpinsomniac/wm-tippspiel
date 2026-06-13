<?php

namespace Tests\Feature;

use App\Models\MatchGame;
use App\Models\Prediction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeaderboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_leaderboard_shows_prediction_details_for_each_participant(): void
    {
        $user = User::factory()->create(['name' => 'Alex']);
        $match = MatchGame::create([
            'home_team' => 'Deutschland',
            'away_team' => 'Kanada',
            'starts_at' => now()->subDay(),
            'stage' => 'Group',
            'home_score' => 2,
            'away_score' => 1,
            'is_final' => true,
        ]);

        Prediction::create([
            'user_id' => $user->id,
            'match_game_id' => $match->id,
            'home_score' => 2,
            'away_score' => 1,
            'points' => 5,
        ]);

        $this->actingAs($user)
            ->get(route('leaderboard.index'))
            ->assertOk()
            ->assertSee('Alex')
            ->assertSee('Deutschland - Kanada')
            ->assertSee('2:1')
            ->assertSee('5');
    }
}
