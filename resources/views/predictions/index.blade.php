@extends('layouts.contest')

@section('content')
    <div class="card"><h2>Meine Tipps</h2></div>
    <table>
        <thead><tr><th>Spiel</th><th>Anpfiff</th><th>Tipp</th><th>Ergebnis</th><th>Punkte</th></tr></thead>
        <tbody>
        @forelse($predictions as $prediction)
            <tr>
                <td>{{ $prediction->matchGame->home_team }} - {{ $prediction->matchGame->away_team }}</td>
                <td>{{ $prediction->matchGame->starts_at->format('d.m.Y H:i') }}</td>
                <td>{{ $prediction->home_score }}:{{ $prediction->away_score }}</td>
                <td>{{ $prediction->matchGame->is_final ? $prediction->matchGame->home_score.':'.$prediction->matchGame->away_score : '-' }}</td>
                <td>{{ $prediction->points }}</td>
            </tr>
        @empty
            <tr><td colspan="5">Noch keine Tipps vorhanden.</td></tr>
        @endforelse
        </tbody>
    </table>
@endsection
