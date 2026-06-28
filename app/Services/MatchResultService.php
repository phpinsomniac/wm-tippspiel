<?php

namespace App\Services;

use App\Models\MatchGame;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MatchResultService
{
    public function fetchAndStoreResults(string $leagueShortcut = 'wm2026'): void
    {
        try {
            $response = Http::get("https://api.openligadb.de/getmatchdata/{$leagueShortcut}");

            if ($response->failed()) {
                Log::error("Fehler beim Abrufen der Matchdaten von OpenLigaDB: " . $response->status());
                return;
            }

            $matches = $response->json();

            foreach ($matches as $matchData) {
                $this->updateMatch($matchData);
            }
        } catch (\Exception $e) {
            Log::error("Exception beim Abrufen der Matchdaten: " . $e->getMessage());
        }
    }

    protected function updateMatch(array $matchData): void
    {
        $homeTeamName = $matchData['team1']['teamName'] ?? '';
        $awayTeamName = $matchData['team2']['teamName'] ?? '';

        $match = $this->findMatch($homeTeamName, $awayTeamName);

        if (! $match) {
            $match = $this->createFinalRoundMatch($matchData, $homeTeamName, $awayTeamName);

            if (! $match) {
                Log::debug("Match nicht gefunden: {$homeTeamName} - {$awayTeamName}");
                return;
            }
        }

        if (! empty($matchData['matchDateTime'])) {
            $match->starts_at = CarbonImmutable::parse($matchData['matchDateTime']);
            $match->save();
        }

        // ResultTypeID 2 is the final result in OpenLigaDB. The separate
        // matchIsFinished flag can lag behind, so the final score is authoritative.
        $finalResult = collect($matchData['matchResults'] ?? [])
            ->first(fn($res) => (int) ($res['resultTypeID'] ?? 0) === 2);

        if (! $finalResult) {
            if ($matchData['matchIsFinished'] ?? false) {
                Log::warning("Finales Match ohne Endergebnis in OpenLigaDB: {$homeTeamName} - {$awayTeamName}");
            }

            return;
        }

        $homeScore = (int) $finalResult['pointsTeam1'];
        $awayScore = (int) $finalResult['pointsTeam2'];
        $scoreChanged = $match->home_score !== $homeScore
            || $match->away_score !== $awayScore;

        $match->home_score = $homeScore;
        $match->away_score = $awayScore;
        $match->is_final = true;
        $match->save();

        if ($scoreChanged || $match->wasChanged('is_final')) {
            $this->updatePredictions($match);
        }
    }

    protected function findMatch(string $homeApi, string $awayApi): ?MatchGame
    {
        $home = $this->normalizeTeamName($homeApi);
        $away = $this->normalizeTeamName($awayApi);

        $match = MatchGame::where('home_team', $home)
            ->where('away_team', $away)
            ->first();

        if ($match) {
            return $match;
        }

        return MatchGame::all()
            ->first(fn (MatchGame $match) => $this->normalizeTeamName($match->home_team) === $home
                && $this->normalizeTeamName($match->away_team) === $away);
    }

    protected function createFinalRoundMatch(array $matchData, string $homeApi, string $awayApi): ?MatchGame
    {
        if ($this->isPlaceholderTeam($homeApi) || $this->isPlaceholderTeam($awayApi)) {
            return null;
        }

        $stage = $this->stageFromApi($matchData);

        if (! $this->isFinalRoundStage($stage['stage'], $stage['group_name'])) {
            return null;
        }

        if (empty($matchData['matchDateTime'])) {
            return null;
        }

        return MatchGame::create([
            'home_team' => $this->normalizeTeamName($homeApi),
            'away_team' => $this->normalizeTeamName($awayApi),
            'starts_at' => CarbonImmutable::parse($matchData['matchDateTime']),
            'stage' => $stage['stage'],
            'group_name' => $stage['group_name'],
            'is_final' => false,
        ]);
    }

    protected function normalizeTeamName(string $teamName): string
    {
        if (preg_match('/^t.*rkei$/i', $teamName) || preg_match('/^t.*rkiye$/i', $teamName)) {
            return 'Turkiye';
        }

        $mapping = [
            'Kanada' => 'Canada',
            'Mexiko' => 'Mexico',
            'SÃ¼dafrika' => 'South Africa',
            'SÃ¼dkorea' => 'Korea Republic',
            'Tschechien' => 'Czechia',
            'Bosnien-Herzegowina' => 'Bosnia & Herzegovina',
            'TÃ¼rkei' => 'TÃ¼rkiye',
            'Australien' => 'Australia',
            'Schweiz' => 'Switzerland',
            'Brasilien' => 'Brazil',
            'Katar' => 'Qatar',
            'ElfenbeinkÃ¼ste' => 'CÃ´te d\'Ivoire',
            'Deutschland' => 'Germany',
            'Niederlande' => 'Netherlands',
            'Schweden' => 'Sweden',
            'Tunesien' => 'Tunisia',
            'Saudi-Arabien' => 'Saudi Arabia',
            'Spanien' => 'Spain',
            'Kap Verde' => 'Cabo Verde',
            'Iran' => 'IR Iran',
            'Neuseeland' => 'New Zealand',
            'Belgien' => 'Belgium',
            'Ã„gypten' => 'Egypt',
            'Frankreich' => 'France',
            'Irak' => 'Iraq',
            'Norwegen' => 'Norway',
            'Argentinien' => 'Argentina',
            'Ã–sterreich' => 'Austria',
            'Jordanien' => 'Jordan',
            'Algerien' => 'Algeria',
            'Ghana' => 'Ghana',
            'Panama' => 'Panama',
            'Kroatien' => 'Croatia',
            'Portugal' => 'Portugal',
            'DR Kongo' => 'Congo DR',
            'Usbekistan' => 'Uzbekistan',
            'Kolumbien' => 'Colombia',
            'Schottland' => 'Scotland',
            'Marokko' => 'Morocco',
            'Haiti' => 'Haiti',
            'Ecuador' => 'Ecuador',
            'Italien' => 'Italy',
        ];

        $legacyEncodingMapping = [
            "T\xc3\x83\xc2\xbcrkei" => "T\xc3\x83\xc2\xbcrkiye",
        ];

        return $mapping[$teamName] ?? $legacyEncodingMapping[$teamName] ?? $teamName;
    }

    protected function stageFromApi(array $matchData): array
    {
        $groupName = $matchData['group']['groupName']
            ?? $matchData['groupName']
            ?? null;

        if (! $groupName) {
            return ['stage' => 'Endrunde', 'group_name' => null];
        }

        if ($this->isGroupStageName($groupName)) {
            return ['stage' => 'Gruppenphase', 'group_name' => $groupName];
        }

        return ['stage' => $groupName, 'group_name' => null];
    }

    protected function isGroupStageName(string $name): bool
    {
        $name = mb_strtolower($name);

        return str_contains($name, 'gruppe')
            || str_contains($name, 'group')
            || str_contains($name, 'vorrunde')
            || str_contains($name, 'gruppenphase');
    }

    protected function isFinalRoundStage(string $stage, ?string $groupName): bool
    {
        $match = new MatchGame([
            'stage' => $stage,
            'group_name' => $groupName,
        ]);

        return $match->isFinalRound();
    }

    protected function isPlaceholderTeam(string $teamName): bool
    {
        $name = trim(mb_strtolower($teamName));

        return $name === ''
            || str_contains($name, 'tbd')
            || str_contains($name, 'offen')
            || str_contains($name, 'winner')
            || str_contains($name, 'sieger')
            || str_contains($name, 'platzhalter')
            || str_contains($name, 'noch nicht');
    }

    protected function updatePredictions(MatchGame $match): void
    {
        $predictions = $match->predictions;

        foreach ($predictions as $prediction) {
            $prediction->points = $prediction->calculatePoints();
            $prediction->save();
        }
    }
}
