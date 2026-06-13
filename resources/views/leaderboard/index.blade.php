@extends('layouts.contest')

@section('content')
    <style>
        .leaderboard-list { display: grid; gap: 10px; }
        .leaderboard-header, .leader-summary { display: grid; grid-template-columns: 80px minmax(180px, 1fr) 100px 100px; gap: 12px; align-items: center; }
        .leaderboard-header { color: #6b7280; font-weight: 700; padding: 0 18px; }
        .leader-entry { background: white; border-radius: 14px; box-shadow: 0 8px 24px rgba(15, 23, 42, .08); overflow: hidden; }
        .leader-entry summary { cursor: pointer; list-style-position: inside; padding: 14px 18px; }
        .leader-entry summary::marker { color: #2563eb; }
        .leader-details { border-top: 1px solid #e5e7eb; padding: 0 18px 18px; overflow-x: auto; }
        .leader-details table { box-shadow: none; border-radius: 0; }
        .leader-details th, .leader-details td { padding: 10px 8px; }
        .points-pill { display: inline-block; min-width: 34px; border-radius: 999px; background: #ecfdf5; color: #065f46; padding: 4px 8px; text-align: center; font-weight: 700; }
        @media (max-width: 700px) {
            .leaderboard-header { display: none; }
            .leader-summary { grid-template-columns: 48px minmax(0, 1fr) 70px; }
            .leader-summary .tips-count { display: none; }
        }
    </style>

    <div class="card">
        <h2>Rangliste</h2>
        <p class="muted">Sortiert nach Gesamtpunkten. Bei Gleichstand alphabetisch.</p>
    </div>

    @if($leaders->isNotEmpty())
        <div class="leaderboard-list">
            <div class="leaderboard-header">
                <div>Rang</div>
                <div>Name</div>
                <div>Tipps</div>
                <div>Punkte</div>
            </div>

            @foreach($leaders as $index => $leader)
                <details class="leader-entry">
                    <summary>
                        <span class="leader-summary">
                            <span>{{ $index + 1 }}</span>
                            <strong>{{ $leader->name }}</strong>
                            <span class="tips-count">{{ $leader->tips_count }}</span>
                            <strong>{{ $leader->total_points }}</strong>
                        </span>
                    </summary>

                    <div class="leader-details">
                        <table>
                            <thead>
                            <tr>
                                <th>Spiel</th>
                                <th>Anpfiff</th>
                                <th>Ergebnis</th>
                                <th>Tipp</th>
                                <th>Punkte</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($leader->predictions as $prediction)
                                <tr>
                                    <td>{{ $prediction->matchGame->home_team }} - {{ $prediction->matchGame->away_team }}</td>
                                    <td>{{ $prediction->matchGame->starts_at->format('d.m.Y H:i') }}</td>
                                    <td>
                                        {{ $prediction->matchGame->is_final ? $prediction->matchGame->home_score.':'.$prediction->matchGame->away_score : '-' }}
                                    </td>
                                    <td>{{ $prediction->home_score }}:{{ $prediction->away_score }}</td>
                                    <td><span class="points-pill">{{ $prediction->points }}</span></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </details>
            @endforeach
        </div>
    @else
        <div class="card">Noch keine Tipps in der Wertung.</div>
    @endif
@endsection
