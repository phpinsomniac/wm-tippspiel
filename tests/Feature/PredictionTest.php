<?php

namespace Tests\Feature;

use App\Models\MatchGame;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PredictionTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_store_multiple_predictions_at_once()
    {
        $user = User::factory()->create();
        $match1 = MatchGame::create([
            'home_team' => 'Team A',
            'away_team' => 'Team B',
            'starts_at' => now()->addDay(),
            'stage' => 'Group',
            'is_final' => false
        ]);
        $match2 = MatchGame::create([
            'home_team' => 'Team C',
            'away_team' => 'Team D',
            'starts_at' => now()->addDay(),
            'stage' => 'Group',
            'is_final' => false
        ]);

        $response = $this->actingAs($user)->post(route('predictions.storeAll'), [
            'predictions' => [
                '0-0' => [
                    'match_game_id' => $match1->id,
                    'home_score' => 2,
                    'away_score' => 1,
                ],
                '0-1' => [
                    'match_game_id' => $match2->id,
                    'home_score' => 0,
                    'away_score' => 3,
                ]
            ]
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('predictions', [
            'user_id' => $user->id,
            'match_game_id' => $match1->id,
            'home_score' => 2,
            'away_score' => 1,
        ]);
        $this->assertDatabaseHas('predictions', [
            'user_id' => $user->id,
            'match_game_id' => $match2->id,
            'home_score' => 0,
            'away_score' => 3,
        ]);
    }

    public function test_user_can_still_store_single_prediction()
    {
        $user = User::factory()->create();
        $match = MatchGame::create([
            'home_team' => 'Team A',
            'away_team' => 'Team B',
            'starts_at' => now()->addDay(),
            'stage' => 'Group',
            'is_final' => false
        ]);

        $response = $this->actingAs($user)->post(route('predictions.store', $match), [
            'home_score' => 1,
            'away_score' => 2,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('predictions', [
            'user_id' => $user->id,
            'match_game_id' => $match->id,
            'home_score' => 1,
            'away_score' => 2,
        ]);
    }

    public function test_user_can_store_single_prediction_from_mass_form()
    {
        $user = User::factory()->create();
        $match = MatchGame::create([
            'home_team' => 'Team A',
            'away_team' => 'Team B',
            'starts_at' => now()->addDay(),
            'stage' => 'Group',
            'is_final' => false
        ]);

        $response = $this->actingAs($user)->post(route('predictions.store', $match), [
            'predictions' => [
                'some-index' => [
                    'match_game_id' => $match->id,
                    'home_score' => 3,
                    'away_score' => 2,
                ]
            ]
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('predictions', [
            'user_id' => $user->id,
            'match_game_id' => $match->id,
            'home_score' => 3,
            'away_score' => 2,
        ]);
    }
}
