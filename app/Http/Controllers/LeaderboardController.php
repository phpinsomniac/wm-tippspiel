<?php

namespace App\Http\Controllers;

use App\Models\Prediction;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LeaderboardController extends Controller
{
    public function index(): View
    {
        $leaders = Prediction::query()
            ->select('users.id', 'users.name', DB::raw('SUM(predictions.points) as total_points'), DB::raw('COUNT(predictions.id) as tips_count'))
            ->join('users', 'users.id', '=', 'predictions.user_id')
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('total_points')
            ->orderBy('users.name')
            ->get();

        return view('leaderboard.index', compact('leaders'));
    }
}
