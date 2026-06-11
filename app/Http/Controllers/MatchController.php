<?php

namespace App\Http\Controllers;

use App\Models\MatchGame;
use Illuminate\View\View;

class MatchController extends Controller
{
    public function index(): View
    {
        $matches = MatchGame::with(['predictions' => fn ($query) => $query->where('user_id', auth()->id())])
            ->orderBy('starts_at')
            ->get()
            ->groupBy(fn (MatchGame $match) => $match->stage.' '.($match->group_name ? '· '.$match->group_name : ''));

        return view('matches.index', compact('matches'));
    }
}
