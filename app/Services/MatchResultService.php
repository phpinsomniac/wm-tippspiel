<?php

namespace App\Services;

use App\Models\MatchGame;
use App\Models\Prediction;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MatchResultService
{
    /**
     * Ruft die Ergebnisse von OpenLigaDB ab.
     * WM 2026 League Shortcut: 'wm2026' (angenommen, muss evtl. angepasst werden)
     * Aktuell nutzen wir 'wm2022' oder ähnliches zum Testen, falls 'wm2026' noch keine Daten hat,
     * aber für das Ziel ist 'wm2026' relevant.
     */
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
        $homeTeamName = $matchData['team1']['teamName'];
        $awayTeamName = $matchData['team2']['teamName'];

        // Versuche Mapping oder direkte Suche
        $match = $this->findMatch($homeTeamName, $awayTeamName);

        if (! $match) {
            // Logge fehlende Zuordnung für Debugging
            Log::debug("Match nicht gefunden: {$homeTeamName} - {$awayTeamName}");
            return;
        }

        // Wenn das Spiel bereits als final markiert ist, überspringen wir es
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
        $mapping = [
            'Kanada' => 'Canada',
            'Mexiko' => 'Mexico',
            'Südafrika' => 'South Africa',
            'Südkorea' => 'Korea Republic',
            'Tschechien' => 'Czechia',
            'Bosnien-Herzegowina' => 'Bosnia & Herzegovina',
            'Türkei' => 'Türkiye',
            'Australien' => 'Australia',
            'Schweiz' => 'Switzerland',
            'Brasilien' => 'Brazil',
            'Katar' => 'Qatar',
            'Elfenbeinküste' => 'Côte d\'Ivoire',
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
            'Ägypten' => 'Egypt',
            'Frankreich' => 'France',
            'Irak' => 'Iraq',
            'Norwegen' => 'Norway',
            'Argentinien' => 'Argentina',
            'Österreich' => 'Austria',
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
            'Italien' => 'Italy', // Nur vorsorglich
        ];

        $home = $mapping[$homeApi] ?? $homeApi;
        $away = $mapping[$awayApi] ?? $awayApi;

        return MatchGame::where('home_team', $home)
            ->where('away_team', $away)
            ->first();
    }

    protected function updatePredictions(MatchGame $match): void
    {
        // Wir aktualisieren alle Tipps für dieses Match, deren Punkte noch 0 sind
        // (Da wir wissen, dass das Match gerade finalisiert wurde)
        // Oder wir berechnen sie einfach für alle Tipps dieses Matches neu.
        $predictions = $match->predictions;

        foreach ($predictions as $prediction) {
            $prediction->points = $prediction->calculatePoints();
            $prediction->save();
        }
    }
}
