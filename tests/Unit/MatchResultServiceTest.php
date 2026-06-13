<?php

namespace Tests\Unit;

use App\Models\MatchGame;
use App\Models\User;
use App\Services\MatchResultService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MatchResultServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_maps_openligadb_german_canada_name_and_updates_result(): void
    {
        $match = MatchGame::create([
            'home_team' => 'Canada',
            'away_team' => 'Bosnia & Herzegovina',
            'starts_at' => now()->subHour(),
            'stage' => 'Gruppenphase',
            'is_final' => false,
        ]);
        $prediction = User::factory()->create()->predictions()->create([
            'match_game_id' => $match->id,
            'home_score' => 1,
            'away_score' => 1,
        ]);

        Http::fake([
            'api.openligadb.de/getmatchdata/wm2026' => Http::response([
                [
                    'team1' => ['teamName' => 'Kanada'],
                    'team2' => ['teamName' => 'Bosnien-Herzegowina'],
                    'matchIsFinished' => true,
                    'matchResults' => [
                        ['resultTypeID' => 1, 'pointsTeam1' => 0, 'pointsTeam2' => 1],
                        ['resultTypeID' => 2, 'pointsTeam1' => 1, 'pointsTeam2' => 1],
                    ],
                ],
            ]),
        ]);

        app(MatchResultService::class)->fetchAndStoreResults('wm2026');

        $match->refresh();
        $prediction->refresh();

        $this->assertTrue($match->is_final);
        $this->assertSame(1, $match->home_score);
        $this->assertSame(1, $match->away_score);
        $this->assertSame(5, $prediction->points);
    }
}
