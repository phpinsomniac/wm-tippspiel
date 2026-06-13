<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\View\View;

class LeaderboardController extends Controller
{
    public function index(): View
    {
        $leaders = User::query()
            ->has('predictions')
            ->withCount('predictions as tips_count')
            ->withSum('predictions as total_points', 'points')
            ->with([
                'predictions' => fn ($query) => $query
                    ->select('predictions.*')
                    ->join('match_games', 'match_games.id', '=', 'predictions.match_game_id')
                    ->with('matchGame')
                    ->orderBy('match_games.starts_at')
                    ->orderBy('match_games.id'),
            ])
            ->orderByDesc('total_points')
            ->orderBy('name')
            ->get();

        return view('leaderboard.index', compact('leaders'));
    }
}
