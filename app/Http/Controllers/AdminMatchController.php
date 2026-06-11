<?php

namespace App\Http\Controllers;

use App\Models\MatchGame;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminMatchController extends Controller
{
    public function index(): View
    {
        $matches = MatchGame::orderBy('starts_at')->paginate(50);

        return view('admin.matches.index', compact('matches'));
    }

    public function create(): View
    {
        return view('admin.matches.form', ['match' => new MatchGame()]);
    }

    public function store(Request $request): RedirectResponse
    {
        MatchGame::create($this->validated($request));

        return redirect()->route('admin.matches.index')->with('status', 'Spiel angelegt.');
    }

    public function edit(MatchGame $match): View
    {
        return view('admin.matches.form', compact('match'));
    }

    public function update(Request $request, MatchGame $match): RedirectResponse
    {
        $data = $this->validated($request);
        $match->update($data);

        if ($match->is_final) {
            $match->predictions()->with('matchGame')->get()->each(function ($prediction) {
                $prediction->update(['points' => $prediction->calculatePoints()]);
            });
        }

        return redirect()->route('admin.matches.index')->with('status', 'Spiel aktualisiert.');
    }

    public function destroy(MatchGame $match): RedirectResponse
    {
        $match->delete();

        return back()->with('status', 'Spiel geloescht.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'group_name' => ['nullable', 'string', 'max:50'],
            'stage' => ['required', 'string', 'max:80'],
            'home_team' => ['required', 'string', 'max:100'],
            'away_team' => ['required', 'string', 'max:100'],
            'starts_at' => ['required', 'date'],
            'home_score' => ['nullable', 'integer', 'min:0', 'max:30'],
            'away_score' => ['nullable', 'integer', 'min:0', 'max:30'],
            'is_final' => ['nullable', 'boolean'],
        ]) + ['is_final' => false];
    }
}
