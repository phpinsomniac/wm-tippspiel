@extends('layouts.contest')

@section('content')
    <div class="card row" style="justify-content: space-between">
        <h2 style="margin:0">Spiele verwalten</h2>
        <a class="button" href="{{ route('admin.matches.create') }}">Spiel anlegen</a>
    </div>
    <table>
        <thead><tr><th>Anpfiff</th><th>Spiel</th><th>Phase</th><th>Ergebnis</th><th>Status</th><th></th></tr></thead>
        <tbody>
        @foreach($matches as $match)
            <tr>
                <td>{{ $match->starts_at->format('d.m.Y H:i') }}</td>
                <td>{{ $match->home_team }} - {{ $match->away_team }}</td>
                <td>{{ $match->stage }} {{ $match->group_name }}</td>
                <td>{{ $match->home_score !== null ? $match->home_score.':'.$match->away_score : '-' }}</td>
                <td>{{ $match->is_final ? 'Final' : 'Offen' }}</td>
                <td class="row">
                    <a class="button" href="{{ route('admin.matches.edit', $match) }}">Bearbeiten</a>
                    <form method="post" action="{{ route('admin.matches.destroy', $match) }}" onsubmit="return confirm('Spiel loeschen?')">
                        @csrf @method('delete')
                        <button class="danger">Loeschen</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div style="margin-top:16px">{{ $matches->links() }}</div>
@endsection
