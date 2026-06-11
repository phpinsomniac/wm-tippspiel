@extends('layouts.contest')

@section('content')
    <div class="card">
        <h2>Rangliste</h2>
        <p class="muted">Sortiert nach Gesamtpunkten. Bei Gleichstand alphabetisch.</p>
    </div>
    <table>
        <thead><tr><th>Rang</th><th>Name</th><th>Tipps</th><th>Punkte</th></tr></thead>
        <tbody>
        @forelse($leaders as $index => $leader)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $leader->name }}</td>
                <td>{{ $leader->tips_count }}</td>
                <td><strong>{{ $leader->total_points }}</strong></td>
            </tr>
        @empty
            <tr><td colspan="4">Noch keine Tipps in der Wertung.</td></tr>
        @endforelse
        </tbody>
    </table>
@endsection
