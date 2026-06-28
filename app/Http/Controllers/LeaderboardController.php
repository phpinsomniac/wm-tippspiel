<?php

namespace App\Http\Controllers;

use App\Models\MatchGame;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeaderboardController extends Controller
{
    public function index(Request $request): View
    {
        $round = $request->query('round') === MatchGame::ROUND_FINAL
            ? MatchGame::ROUND_FINAL
            : MatchGame::ROUND_GROUP;

        $leaders = User::query()
            ->whereHas('predictions.matchGame', fn (Builder $query) => $this->applyRoundFilter($query, $round))
            ->with([
                'predictions' => fn ($query) => $query
                    ->select('predictions.*')
                    ->whereHas('matchGame', fn (Builder $query) => $this->applyRoundFilter($query, $round))
                    ->join('match_games', 'match_games.id', '=', 'predictions.match_game_id')
                    ->with('matchGame')
                    ->orderBy('match_games.starts_at')
                    ->orderBy('match_games.id'),
            ])
            ->get()
            ->each(function (User $user) {
                $user->setAttribute('tips_count', $user->predictions->count());
                $user->setAttribute('total_points', $user->predictions->sum('points'));
            })
            ->sortBy([
                ['total_points', 'desc'],
                ['name', 'asc'],
            ])
            ->values();

        return view('leaderboard.index', compact('leaders', 'round'));
    }

    private function applyRoundFilter(Builder $query, string $round): void
    {
        if ($round === MatchGame::ROUND_FINAL) {
            $query
                ->whereNotIn('stage', MatchGame::GROUP_STAGE_STAGES)
                ->where(function (Builder $query) {
                    $query
                        ->whereNull('group_name')
                        ->orWhere(function (Builder $query) {
                            $query
                                ->where('group_name', 'not like', '%Gruppe%')
                                ->where('group_name', 'not like', '%Group%');
                        });
                });

            return;
        }

        $query->where(function (Builder $query) {
            $query
                ->whereIn('stage', MatchGame::GROUP_STAGE_STAGES)
                ->orWhere('group_name', 'like', '%Gruppe%')
                ->orWhere('group_name', 'like', '%Group%');
        });
    }
}
