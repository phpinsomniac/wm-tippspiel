@extends('layouts.contest')

@section('content')
    <div class="card">
        <h2>Spielplan und Tipps</h2>
        <p class="muted">Tipps koennen bis zum Anpfiff angelegt oder geaendert werden.</p>
    </div>

    <form method="post" action="{{ route('predictions.storeAll') }}">
        @csrf
        @forelse($matches as $stage => $stageMatches)
            <h3>{{ $stage }}</h3>
            @foreach($stageMatches as $match)
                @php($prediction = $match->predictions->first())
                <div class="card">
                    <div class="row" style="justify-content: space-between">
                        <div>
                            <strong>{{ $match->home_team }} vs. {{ $match->away_team }}</strong><br>
                            <span class="muted">{{ $match->starts_at->format('d.m.Y H:i') }} Uhr</span>
                            @if($match->is_final)
                                <div>Ergebnis: <strong>{{ $match->home_score }}:{{ $match->away_score }}</strong></div>
                            @endif
                        </div>
                        @if($match->isPredictionOpen())
                            <div class="row">
                                <input type="hidden" name="predictions[{{ $loop->parent->index }}-{{ $loop->index }}][match_game_id]" value="{{ $match->id }}">
                                <input type="number" name="predictions[{{ $loop->parent->index }}-{{ $loop->index }}][home_score]" min="0" max="30" value="{{ old('predictions.'.$loop->parent->index.'-'.$loop->index.'.home_score', $prediction?->home_score) }}" style="width:70px">
                                <span>:</span>
                                <input type="number" name="predictions[{{ $loop->parent->index }}-{{ $loop->index }}][away_score]" min="0" max="30" value="{{ old('predictions.'.$loop->parent->index.'-'.$loop->index.'.away_score', $prediction?->away_score) }}" style="width:70px">
                                <button type="submit" formaction="{{ route('predictions.store', $match) }}" name="single_save" value="1" title="Nur dieses Spiel speichern">💾</button>
                            </div>
                        @else
                            <div>
                                Dein Tipp:
                                <strong>{{ $prediction ? $prediction->home_score.':'.$prediction->away_score : '-' }}</strong>
                                @if($match->is_final)
                                    <br><span class="muted">Punkte: {{ $prediction?->points ?? 0 }}</span>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        @empty
            <div class="card">Noch keine Spiele angelegt.</div>
        @endforelse

        @if($matches->isNotEmpty())
            <div class="card" style="position: sticky; bottom: 1rem; z-index: 100; background: #fff; box-shadow: 0 -2px 10px rgba(0,0,0,0.1);">
                <div class="row" style="justify-content: center;">
                    <button type="submit" style="padding: 1rem 2rem; font-size: 1.1rem;">Alle Tipps speichern</button>
                </div>
            </div>
        @endif
    </form>
@endsection
