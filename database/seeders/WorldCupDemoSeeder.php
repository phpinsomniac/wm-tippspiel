<?php

namespace Database\Seeders;

use App\Models\MatchGame;
use Illuminate\Database\Seeder;

class WorldCupDemoSeeder extends Seeder
{
    public function run(): void
    {
        $games = [
            ['Gruppe A', 'Gruppenphase', 'Deutschland', 'Kanada', '2026-06-11 18:00:00'],
            ['Gruppe A', 'Gruppenphase', 'Mexiko', 'Suedafrika', '2026-06-11 21:00:00'],
            ['Gruppe B', 'Gruppenphase', 'Spanien', 'Japan', '2026-06-12 18:00:00'],
            ['Gruppe B', 'Gruppenphase', 'Brasilien', 'Marokko', '2026-06-12 21:00:00'],
            [null, 'Achtelfinale', 'Sieger Gruppe A', 'Zweiter Gruppe B', '2026-06-28 18:00:00'],
            [null, 'Finale', 'Finalist 1', 'Finalist 2', '2026-07-19 21:00:00'],
        ];

        foreach ($games as [$group, $stage, $home, $away, $startsAt]) {
            MatchGame::firstOrCreate([
                'home_team' => $home,
                'away_team' => $away,
                'starts_at' => $startsAt,
            ], [
                'group_name' => $group,
                'stage' => $stage,
            ]);
        }
    }
}
