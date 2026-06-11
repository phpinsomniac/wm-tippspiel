<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'WM Gewinnspiel') }}</title>
    <style>
        body { font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; margin: 0; background: #f6f7fb; color: #172033; }
        header { background: #111827; color: white; padding: 18px 28px; }
        nav a { color: white; margin-right: 18px; text-decoration: none; font-weight: 600; }
        main { max-width: 1100px; margin: 28px auto; padding: 0 18px; }
        .card { background: white; border-radius: 14px; box-shadow: 0 8px 24px rgba(15, 23, 42, .08); padding: 18px; margin-bottom: 18px; }
        .row { display: flex; gap: 12px; align-items: center; flex-wrap: wrap; }
        input, select { border: 1px solid #d1d5db; border-radius: 8px; padding: 8px 10px; }
        button, .button { background: #2563eb; color: white; border: 0; border-radius: 8px; padding: 9px 13px; cursor: pointer; text-decoration: none; display: inline-block; }
        .danger { background: #dc2626; }
        .muted { color: #6b7280; }
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 14px; overflow: hidden; }
        th, td { padding: 12px; border-bottom: 1px solid #e5e7eb; text-align: left; }
        .status { background: #ecfdf5; color: #065f46; padding: 10px 12px; border-radius: 8px; margin-bottom: 16px; }
        .error { background: #fef2f2; color: #991b1b; padding: 10px 12px; border-radius: 8px; margin-bottom: 16px; }
    </style>
</head>
<body>
<header>
    <h1 style="margin:0 0 10px">WM Gewinnspiel</h1>
    <nav>
        <a href="{{ route('matches.index') }}">Spielplan</a>
        <a href="{{ route('predictions.index') }}">Meine Tipps</a>
        <a href="{{ route('leaderboard.index') }}">Rangliste</a>
        @auth
            @if(auth()->user()->is_admin)
                <a href="{{ route('admin.matches.index') }}">Admin</a>
            @endif
        @endauth
    </nav>
</header>
<main>
    @if(session('status')) <div class="status">{{ session('status') }}</div> @endif
    @if($errors->any())
        <div class="error">
            <strong>Bitte pruefen:</strong>
            <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif
    @yield('content')
</main>
</body>
</html>
