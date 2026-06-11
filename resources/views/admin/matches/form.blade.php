@extends('layouts.contest')

@section('content')
    <div class="card">
        <h2>{{ $match->exists ? 'Spiel bearbeiten' : 'Spiel anlegen' }}</h2>
        <form method="post" action="{{ $match->exists ? route('admin.matches.update', $match) : route('admin.matches.store') }}">
            @csrf
            @if($match->exists) @method('put') @endif

            <p><label>Phase<br><input name="stage" value="{{ old('stage', $match->stage ?: 'Gruppenphase') }}" required></label></p>
            <p><label>Gruppe<br><input name="group_name" value="{{ old('group_name', $match->group_name) }}"></label></p>
            <p><label>Heimteam<br><input name="home_team" value="{{ old('home_team', $match->home_team) }}" required></label></p>
            <p><label>Auswaertsteam<br><input name="away_team" value="{{ old('away_team', $match->away_team) }}" required></label></p>
            <p><label>Anpfiff<br><input type="datetime-local" name="starts_at" value="{{ old('starts_at', $match->starts_at?->format('Y-m-d\\TH:i')) }}" required></label></p>
            <div class="row">
                <label>Heimtore<br><input type="number" min="0" max="30" name="home_score" value="{{ old('home_score', $match->home_score) }}"></label>
                <label>Auswaertstore<br><input type="number" min="0" max="30" name="away_score" value="{{ old('away_score', $match->away_score) }}"></label>
            </div>
            <p><label><input type="checkbox" name="is_final" value="1" @checked(old('is_final', $match->is_final))> Ergebnis ist final und Punkte berechnen</label></p>
            <button>Speichern</button>
            <a class="button" href="{{ route('admin.matches.index') }}" style="background:#6b7280">Zurueck</a>
        </form>
    </div>
@endsection
