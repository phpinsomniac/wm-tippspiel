<?php

namespace Database\Seeders;

use App\Models\MatchGame;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;

class WorldCup2026GroupStageSeeder extends Seeder
{
    /**
     * Seeds the FIFA World Cup 2026 group stage.
     *
     * Source: FIFA World Cup 2026 Match Schedule, English PDF, v17, 10 April 2026.
     * https://digitalhub.fifa.com/m/1be9ce37eb98fcc5/original/FWC26-Match-Schedule_English.pdf
     *
     * The FIFA PDF states: "All times are Eastern Time (ET)."
     * The Seeder converts all kick-off times from America/New_York to config('app.timezone').
     * For Germany, set APP_TIMEZONE=Europe/Berlin in your .env.
     */
    public function run(): void
    {
        $sourceTimezone = 'America/New_York';
        $targetTimezone = config('app.timezone', 'UTC');

        $games = [
            ['no' => 1,  'group' => 'A', 'kickoff_et' => '2026-06-11 15:00', 'home' => 'Mexico',                  'away' => 'South Africa'],
            ['no' => 2,  'group' => 'A', 'kickoff_et' => '2026-06-11 22:00', 'home' => 'Korea Republic',          'away' => 'Czechia'],
            ['no' => 3,  'group' => 'B', 'kickoff_et' => '2026-06-12 15:00', 'home' => 'Canada',                  'away' => 'Bosnia & Herzegovina'],
            ['no' => 4,  'group' => 'D', 'kickoff_et' => '2026-06-12 21:00', 'home' => 'USA',                     'away' => 'Paraguay'],
            ['no' => 5,  'group' => 'C', 'kickoff_et' => '2026-06-13 21:00', 'home' => 'Haiti',                   'away' => 'Scotland'],
            ['no' => 6,  'group' => 'D', 'kickoff_et' => '2026-06-14 00:00', 'home' => 'Australia',               'away' => 'Türkiye'],
            ['no' => 7,  'group' => 'C', 'kickoff_et' => '2026-06-13 18:00', 'home' => 'Brazil',                  'away' => 'Morocco'],
            ['no' => 8,  'group' => 'B', 'kickoff_et' => '2026-06-13 15:00', 'home' => 'Qatar',                   'away' => 'Switzerland'],
            ['no' => 9,  'group' => 'E', 'kickoff_et' => '2026-06-14 19:00', 'home' => "Côte d'Ivoire",          'away' => 'Ecuador'],
            ['no' => 10, 'group' => 'E', 'kickoff_et' => '2026-06-14 13:00', 'home' => 'Germany',                 'away' => 'Curaçao'],
            ['no' => 11, 'group' => 'F', 'kickoff_et' => '2026-06-14 16:00', 'home' => 'Netherlands',             'away' => 'Japan'],
            ['no' => 12, 'group' => 'F', 'kickoff_et' => '2026-06-14 22:00', 'home' => 'Sweden',                  'away' => 'Tunisia'],
            ['no' => 13, 'group' => 'H', 'kickoff_et' => '2026-06-15 18:00', 'home' => 'Saudi Arabia',            'away' => 'Uruguay'],
            ['no' => 14, 'group' => 'H', 'kickoff_et' => '2026-06-15 12:00', 'home' => 'Spain',                   'away' => 'Cabo Verde'],
            ['no' => 15, 'group' => 'G', 'kickoff_et' => '2026-06-15 21:00', 'home' => 'IR Iran',                 'away' => 'New Zealand'],
            ['no' => 16, 'group' => 'G', 'kickoff_et' => '2026-06-15 15:00', 'home' => 'Belgium',                 'away' => 'Egypt'],
            ['no' => 17, 'group' => 'I', 'kickoff_et' => '2026-06-16 15:00', 'home' => 'France',                  'away' => 'Senegal'],
            ['no' => 18, 'group' => 'I', 'kickoff_et' => '2026-06-16 18:00', 'home' => 'Iraq',                    'away' => 'Norway'],
            ['no' => 19, 'group' => 'J', 'kickoff_et' => '2026-06-16 21:00', 'home' => 'Argentina',               'away' => 'Algeria'],
            ['no' => 20, 'group' => 'J', 'kickoff_et' => '2026-06-16 00:00', 'home' => 'Austria',                 'away' => 'Jordan'],
            ['no' => 21, 'group' => 'L', 'kickoff_et' => '2026-06-17 19:00', 'home' => 'Ghana',                   'away' => 'Panama'],
            ['no' => 22, 'group' => 'L', 'kickoff_et' => '2026-06-17 16:00', 'home' => 'England',                 'away' => 'Croatia'],
            ['no' => 23, 'group' => 'K', 'kickoff_et' => '2026-06-17 13:00', 'home' => 'Portugal',                'away' => 'Congo DR'],
            ['no' => 24, 'group' => 'K', 'kickoff_et' => '2026-06-17 22:00', 'home' => 'Uzbekistan',              'away' => 'Colombia'],
            ['no' => 25, 'group' => 'A', 'kickoff_et' => '2026-06-18 12:00', 'home' => 'Czechia',                 'away' => 'South Africa'],
            ['no' => 26, 'group' => 'B', 'kickoff_et' => '2026-06-18 15:00', 'home' => 'Switzerland',             'away' => 'Bosnia & Herzegovina'],
            ['no' => 27, 'group' => 'B', 'kickoff_et' => '2026-06-18 18:00', 'home' => 'Canada',                  'away' => 'Qatar'],
            ['no' => 28, 'group' => 'A', 'kickoff_et' => '2026-06-18 21:00', 'home' => 'Mexico',                  'away' => 'Korea Republic'],
            ['no' => 29, 'group' => 'C', 'kickoff_et' => '2026-06-19 20:30', 'home' => 'Brazil',                  'away' => 'Haiti'],
            ['no' => 30, 'group' => 'C', 'kickoff_et' => '2026-06-19 18:00', 'home' => 'Scotland',                'away' => 'Morocco'],
            ['no' => 31, 'group' => 'D', 'kickoff_et' => '2026-06-19 23:00', 'home' => 'Türkiye',                 'away' => 'Paraguay'],
            ['no' => 32, 'group' => 'D', 'kickoff_et' => '2026-06-19 15:00', 'home' => 'USA',                     'away' => 'Australia'],
            ['no' => 33, 'group' => 'E', 'kickoff_et' => '2026-06-20 16:00', 'home' => 'Germany',                 'away' => "Côte d'Ivoire"],
            ['no' => 34, 'group' => 'E', 'kickoff_et' => '2026-06-20 20:00', 'home' => 'Ecuador',                 'away' => 'Curaçao'],
            ['no' => 35, 'group' => 'F', 'kickoff_et' => '2026-06-20 13:00', 'home' => 'Netherlands',             'away' => 'Sweden'],
            ['no' => 36, 'group' => 'F', 'kickoff_et' => '2026-06-20 00:00', 'home' => 'Tunisia',                 'away' => 'Japan'],
            ['no' => 37, 'group' => 'H', 'kickoff_et' => '2026-06-21 18:00', 'home' => 'Uruguay',                 'away' => 'Cabo Verde'],
            ['no' => 38, 'group' => 'H', 'kickoff_et' => '2026-06-21 12:00', 'home' => 'Spain',                   'away' => 'Saudi Arabia'],
            ['no' => 39, 'group' => 'G', 'kickoff_et' => '2026-06-21 15:00', 'home' => 'Belgium',                 'away' => 'IR Iran'],
            ['no' => 40, 'group' => 'G', 'kickoff_et' => '2026-06-21 21:00', 'home' => 'New Zealand',             'away' => 'Egypt'],
            ['no' => 41, 'group' => 'I', 'kickoff_et' => '2026-06-22 20:00', 'home' => 'Norway',                  'away' => 'Senegal'],
            ['no' => 42, 'group' => 'I', 'kickoff_et' => '2026-06-22 17:00', 'home' => 'France',                  'away' => 'Iraq'],
            ['no' => 43, 'group' => 'J', 'kickoff_et' => '2026-06-22 13:00', 'home' => 'Argentina',               'away' => 'Austria'],
            ['no' => 44, 'group' => 'J', 'kickoff_et' => '2026-06-22 23:00', 'home' => 'Jordan',                  'away' => 'Algeria'],
            ['no' => 45, 'group' => 'L', 'kickoff_et' => '2026-06-23 16:00', 'home' => 'England',                 'away' => 'Ghana'],
            ['no' => 46, 'group' => 'L', 'kickoff_et' => '2026-06-23 19:00', 'home' => 'Panama',                  'away' => 'Croatia'],
            ['no' => 47, 'group' => 'K', 'kickoff_et' => '2026-06-23 13:00', 'home' => 'Portugal',                'away' => 'Uzbekistan'],
            ['no' => 48, 'group' => 'K', 'kickoff_et' => '2026-06-23 22:00', 'home' => 'Colombia',                'away' => 'Congo DR'],
            ['no' => 49, 'group' => 'C', 'kickoff_et' => '2026-06-24 18:00', 'home' => 'Scotland',                'away' => 'Brazil'],
            ['no' => 50, 'group' => 'C', 'kickoff_et' => '2026-06-24 18:00', 'home' => 'Morocco',                 'away' => 'Haiti'],
            ['no' => 51, 'group' => 'B', 'kickoff_et' => '2026-06-24 15:00', 'home' => 'Switzerland',             'away' => 'Canada'],
            ['no' => 52, 'group' => 'B', 'kickoff_et' => '2026-06-24 15:00', 'home' => 'Bosnia & Herzegovina',    'away' => 'Qatar'],
            ['no' => 53, 'group' => 'A', 'kickoff_et' => '2026-06-24 21:00', 'home' => 'Czechia',                 'away' => 'Mexico'],
            ['no' => 54, 'group' => 'A', 'kickoff_et' => '2026-06-24 21:00', 'home' => 'South Africa',            'away' => 'Korea Republic'],
            ['no' => 55, 'group' => 'E', 'kickoff_et' => '2026-06-25 16:00', 'home' => 'Curaçao',                 'away' => "Côte d'Ivoire"],
            ['no' => 56, 'group' => 'E', 'kickoff_et' => '2026-06-25 16:00', 'home' => 'Ecuador',                 'away' => 'Germany'],
            ['no' => 57, 'group' => 'F', 'kickoff_et' => '2026-06-25 19:00', 'home' => 'Japan',                   'away' => 'Sweden'],
            ['no' => 58, 'group' => 'F', 'kickoff_et' => '2026-06-25 19:00', 'home' => 'Tunisia',                 'away' => 'Netherlands'],
            ['no' => 59, 'group' => 'D', 'kickoff_et' => '2026-06-25 22:00', 'home' => 'Türkiye',                 'away' => 'USA'],
            ['no' => 60, 'group' => 'D', 'kickoff_et' => '2026-06-25 22:00', 'home' => 'Paraguay',                'away' => 'Australia'],
            ['no' => 61, 'group' => 'I', 'kickoff_et' => '2026-06-26 15:00', 'home' => 'Norway',                  'away' => 'France'],
            ['no' => 62, 'group' => 'I', 'kickoff_et' => '2026-06-26 15:00', 'home' => 'Senegal',                 'away' => 'Iraq'],
            ['no' => 63, 'group' => 'G', 'kickoff_et' => '2026-06-26 23:00', 'home' => 'Egypt',                   'away' => 'IR Iran'],
            ['no' => 64, 'group' => 'G', 'kickoff_et' => '2026-06-26 23:00', 'home' => 'New Zealand',             'away' => 'Belgium'],
            ['no' => 65, 'group' => 'H', 'kickoff_et' => '2026-06-26 20:00', 'home' => 'Cabo Verde',              'away' => 'Saudi Arabia'],
            ['no' => 66, 'group' => 'H', 'kickoff_et' => '2026-06-26 20:00', 'home' => 'Uruguay',                 'away' => 'Spain'],
            ['no' => 67, 'group' => 'L', 'kickoff_et' => '2026-06-27 17:00', 'home' => 'Panama',                  'away' => 'England'],
            ['no' => 68, 'group' => 'L', 'kickoff_et' => '2026-06-27 17:00', 'home' => 'Croatia',                 'away' => 'Ghana'],
            ['no' => 69, 'group' => 'J', 'kickoff_et' => '2026-06-27 22:00', 'home' => 'Algeria',                 'away' => 'Austria'],
            ['no' => 70, 'group' => 'J', 'kickoff_et' => '2026-06-27 22:00', 'home' => 'Jordan',                  'away' => 'Argentina'],
            ['no' => 71, 'group' => 'K', 'kickoff_et' => '2026-06-27 19:30', 'home' => 'Colombia',                'away' => 'Portugal'],
            ['no' => 72, 'group' => 'K', 'kickoff_et' => '2026-06-27 19:30', 'home' => 'Congo DR',                'away' => 'Uzbekistan'],
        ];

        foreach ($games as $game) {
            $startsAt = CarbonImmutable::createFromFormat('Y-m-d H:i', $game['kickoff_et'], $sourceTimezone)
                ->setTimezone($targetTimezone)
                ->format('Y-m-d H:i:s');

            MatchGame::updateOrCreate(
                [
                    'stage' => 'Gruppenphase',
                    'home_team' => $game['home'],
                    'away_team' => $game['away'],
                ],
                [
                    'group_name' => 'Gruppe '.$game['group'],
                    'starts_at' => $startsAt,
                ]
            );
        }
    }
}
