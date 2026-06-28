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

    public function test_leaderboard_can_be_filtered_by_round(): void
    {
        $user = User::factory()->create(['name' => 'Alex']);
        $groupMatch = MatchGame::create([
            'home_team' => 'Deutschland',
            'away_team' => 'Kanada',
            'starts_at' => now()->subDays(2),
            'stage' => 'Gruppenphase',
            'group_name' => 'Gruppe A',
            'home_score' => 2,
            'away_score' => 1,
            'is_final' => true,
        ]);
        $finalMatch = MatchGame::create([
            'home_team' => 'Brasilien',
            'away_team' => 'Marokko',
            'starts_at' => now()->subDay(),
            'stage' => 'Achtelfinale',
            'home_score' => 1,
            'away_score' => 0,
            'is_final' => true,
        ]);

        Prediction::create([
            'user_id' => $user->id,
            'match_game_id' => $groupMatch->id,
            'home_score' => 2,
            'away_score' => 1,
            'points' => 5,
        ]);
        Prediction::create([
            'user_id' => $user->id,
            'match_game_id' => $finalMatch->id,
            'home_score' => 1,
            'away_score' => 0,
            'points' => 7,
        ]);

        $this->actingAs($user)
            ->get(route('leaderboard.index', ['round' => MatchGame::ROUND_GROUP]))
            ->assertOk()
            ->assertSee('Deutschland - Kanada')
            ->assertDontSee('Brasilien - Marokko')
            ->assertSee('5');

        $this->actingAs($user)
            ->get(route('leaderboard.index', ['round' => MatchGame::ROUND_FINAL]))
            ->assertOk()
            ->assertDontSee('Deutschland - Kanada')
            ->assertSee('Brasilien - Marokko')
            ->assertSee('7');
    }
}
