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
                    'matchDateTime' => '2026-06-12T21:00:00',
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
        $this->assertSame('2026-06-12 21:00:00', $match->starts_at->format('Y-m-d H:i:s'));
        $this->assertSame(1, $match->home_score);
        $this->assertSame(1, $match->away_score);
        $this->assertSame(5, $prediction->points);
    }

    public function test_it_updates_kickoff_time_for_unfinished_matches_with_german_team_names(): void
    {
        $match = MatchGame::create([
            'home_team' => 'Australia',
            'away_team' => 'Türkiye',
            'starts_at' => '2026-06-13 06:00:00',
            'stage' => 'Gruppenphase',
            'is_final' => false,
        ]);

        Http::fake([
            'api.openligadb.de/getmatchdata/wm2026' => Http::response([
                [
                    'team1' => ['teamName' => 'Australien'],
                    'team2' => ['teamName' => 'Türkei'],
                    'matchDateTime' => '2026-06-14T06:00:00',
                    'matchIsFinished' => false,
                    'matchResults' => [],
                ],
            ]),
        ]);

        app(MatchResultService::class)->fetchAndStoreResults('wm2026');

        $match->refresh();

        $this->assertFalse($match->is_final);
        $this->assertNull($match->home_score);
        $this->assertNull($match->away_score);
        $this->assertSame('2026-06-14 06:00:00', $match->starts_at->format('Y-m-d H:i:s'));
    }

    public function test_it_updates_already_final_matches_when_the_feed_corrects_the_score(): void
    {
        $match = MatchGame::create([
            'home_team' => 'Canada',
            'away_team' => 'Bosnia & Herzegovina',
            'starts_at' => '2026-06-12 21:00:00',
            'stage' => 'Gruppenphase',
            'home_score' => 1,
            'away_score' => 0,
            'is_final' => true,
        ]);
        $prediction = User::factory()->create()->predictions()->create([
            'match_game_id' => $match->id,
            'home_score' => 1,
            'away_score' => 1,
            'points' => 0,
        ]);

        Http::fake([
            'api.openligadb.de/getmatchdata/wm2026' => Http::response([
                [
                    'team1' => ['teamName' => 'Kanada'],
                    'team2' => ['teamName' => 'Bosnien-Herzegowina'],
                    'matchDateTime' => '2026-06-12T21:00:00',
                    'matchIsFinished' => true,
                    'matchResults' => [
                        ['resultTypeID' => 2, 'pointsTeam1' => 1, 'pointsTeam2' => 1],
                    ],
                ],
            ]),
        ]);

        app(MatchResultService::class)->fetchAndStoreResults('wm2026');

        $match->refresh();
        $prediction->refresh();

        $this->assertSame(1, $match->home_score);
        $this->assertSame(1, $match->away_score);
        $this->assertSame(5, $prediction->points);
    }
}
