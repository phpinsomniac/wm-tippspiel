<?php

namespace App\Http\Controllers;

use App\Models\MatchGame;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MatchController extends Controller
{
    public function index(Request $request): View
    {
        $round = $request->query('round') === MatchGame::ROUND_FINAL
            ? MatchGame::ROUND_FINAL
            : MatchGame::ROUND_GROUP;

        $matches = MatchGame::with(['predictions' => fn ($query) => $query->where('user_id', auth()->id())])
            ->where(fn (Builder $query) => $this->applyRoundFilter($query, $round))
            ->orderBy('starts_at')
            ->get()
            ->groupBy(fn (MatchGame $match) => $match->stage.' '.($match->group_name ? '- '.$match->group_name : ''));

        return view('matches.index', compact('matches', 'round'));
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
