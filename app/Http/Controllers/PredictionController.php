<?php

namespace App\Http\Controllers;

use App\Models\MatchGame;
use App\Models\Prediction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PredictionController extends Controller
{
    public function index(): View
    {
        $predictions = Prediction::with('matchGame')
            ->where('user_id', auth()->id())
            ->join('match_games', 'match_games.id', '=', 'predictions.match_game_id')
            ->select('predictions.*')
            ->orderBy('match_games.starts_at')
            ->orderBy('match_games.id')
            ->get();

        return view('predictions.index', compact('predictions'));
    }

    public function store(Request $request, MatchGame $matchGame): RedirectResponse
    {
        abort_unless($matchGame->isPredictionOpen(), 403, 'Tipps sind fuer dieses Spiel geschlossen.');

        // Falls wir aus dem Massenformular kommen, koennten die Daten verschachtelt sein
        if ($request->has('predictions')) {
            $allPredictions = $request->input('predictions');
            // Suche den passenden Eintrag fuer dieses Match
            $matchData = collect($allPredictions)->firstWhere('match_game_id', (string)$matchGame->id);

            if ($matchData) {
                $request->merge([
                    'home_score' => $matchData['home_score'] ?? null,
                    'away_score' => $matchData['away_score'] ?? null,
                ]);
            }
        }

        $data = $request->validate([
            'home_score' => ['required', 'integer', 'min:0', 'max:30'],
            'away_score' => ['required', 'integer', 'min:0', 'max:30'],
        ]);

        Prediction::updateOrCreate(
            ['user_id' => auth()->id(), 'match_game_id' => $matchGame->id],
            [
                'home_score' => $data['home_score'],
                'away_score' => $data['away_score'],
                'locked_at' => now(),
            ]
        );

        return back()->with('status', 'Tipp gespeichert.');
    }

    public function storeAll(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'predictions' => ['required', 'array'],
            'predictions.*.match_game_id' => ['required', 'exists:match_games,id'],
            'predictions.*.home_score' => ['nullable', 'integer', 'min:0', 'max:30'],
            'predictions.*.away_score' => ['nullable', 'integer', 'min:0', 'max:30'],
        ]);

        $count = 0;
        foreach ($data['predictions'] as $predictionData) {
            if ($predictionData['home_score'] === null || $predictionData['away_score'] === null) {
                continue;
            }

            $matchGame = MatchGame::findOrFail($predictionData['match_game_id']);

            if ($matchGame->isPredictionOpen()) {
                Prediction::updateOrCreate(
                    ['user_id' => auth()->id(), 'match_game_id' => $matchGame->id],
                    [
                        'home_score' => $predictionData['home_score'],
                        'away_score' => $predictionData['away_score'],
                        'locked_at' => now(),
                    ]
                );
                $count++;
            }
        }

        return back()->with('status', $count . ' Tipps gespeichert.');
    }
}
