# WM Gewinnspiel - Laravel App

Firmeninternes WM-Tippspiel / Gewinnspiel fuer Laravel 12 oder Laravel 13.

## Features

- Login/Registrierung ueber Laravel Starter Kit, z. B. Blade, Livewire, React oder Vue
- Admin-Verwaltung fuer WM-Spiele
- Tippabgabe bis zum Anpfiff
- Automatische Punkteberechnung nach Ergebniseingabe
- Rangliste / Leaderboard
- Admin-Rolle per `is_admin`
- Firmen-Domain-Einschraenkung optional per `.env`
- Automatische Aktualisierung der Spielergebnisse via OpenLigaDB API
- Einfache Blade-Oberflaeche ohne Frontend-Build-Zwang

## Punkte-Regeln

- 5 Punkte: exaktes Ergebnis
- 3 Punkte: richtige Tordifferenz, aber nicht exakt
- 2 Punkte: richtige Tendenz, also Sieg/Unentschieden/Niederlage
- 0 Punkte: daneben

## Installation in ein frisches Laravel-Projekt

Die aktuellen Laravel-Dokumente zeigen Starter Kits fuer Authentifizierung; diese App setzt eine vorhandene User-Authentifizierung voraus.

```bash
composer create-project laravel/laravel wm-gewinnspiel
cd wm-gewinnspiel

# Authentifizierung installieren, Beispiel Blade Starter Kit:
composer require laravel/breeze --dev
php artisan breeze:install blade
npm install && npm run build

# Dieses Paket in das Laravel-Projekt kopieren:
cp -R /pfad/zu/wm-gewinnspiel-laravel/* .

# Middleware-Aliasse in bootstrap/app.php eintragen, siehe bootstrap-app-middleware-snippet.php

php artisan migrate
php artisan db:seed --class=WorldCupDemoSeeder
php artisan serve
```

## Middleware registrieren

In `bootstrap/app.php` im `withMiddleware`-Block ergaenzen:

```php
$middleware->alias([
    'admin' => \App\Http\Middleware\EnsureAdmin::class,
    'company.email' => \App\Http\Middleware\EnsureCompanyEmailDomain::class,
]);
```

## Admin aktivieren

Nach Registrierung eines Users:

```bash
php artisan tinker
```

```php
$user = \App\Models\User::where('email', 'admin@firma.de')->first();
$user->is_admin = true;
$user->save();
```

## Optionale Firmen-Domain

In `.env`:

```env
COMPANY_EMAIL_DOMAIN=firma.de
```

Dann laesst sich die Middleware `company.email` auf Registrierungs- oder App-Routen anwenden. Standardmaessig ist sie nicht aktiv, damit das Paket sofort laeuft.

## Automatische Spielergebnisse (Scheduler)

Die App kann Spielergebnisse automatisch von [OpenLigaDB](https://www.openligadb.de/) abrufen und die Punkte der Benutzer berechnen.

### Manueller Abruf

Um die Ergebnisse manuell zu aktualisieren, kann folgender Befehl genutzt werden:

```bash
php artisan app:fetch-match-results
```

Standardmäßig wird die WM 2026 (`wm26`) abgefragt. Für andere Wettbewerbe kann das Kürzel als Argument übergeben werden:

```bash
php artisan app:fetch-match-results bl1
```

### Automatische Aktualisierung einrichten

Damit die Ergebnisse regelmäßig im Hintergrund aktualisiert werden, muss der Laravel Scheduler auf dem Server eingerichtet sein. Hierzu wird ein einziger Cronjob benötigt:

1. Öffne die Crontab des Servers (meist als Web-User, z.B. `www-data`):
   ```bash
   crontab -u www-data -e
   ```

2. Füge folgende Zeile am Ende hinzu (Pfade ggf. anpassen):
   ```cron
   * * * * * cd /pfad/zu/deinem/projekt && php artisan schedule:run >> /dev/null 2>&1
   ```

Die App ist so vorkonfiguriert, dass sie alle 5 Minuten nach neuen Ergebnissen sucht. Die Konfiguration hierfür befindet sich in `routes/console.php`.

Ein API-Key für OpenLigaDB wird nicht benötigt.

## Routen

- `/matches` - Spielplan und Tippabgabe
- `/my-predictions` - eigene Tipps
- `/leaderboard` - Rangliste
- `/admin/matches` - Admin-Verwaltung der Spiele

## Dateien in diesem Paket

- Models: `MatchGame`, `Prediction`
- Controller: Match-/Tipp-/Leaderboard-/Admin-Controller
- Service: `MatchResultService` (API-Integration)
- Middleware: `EnsureCompanyEmailDomain`, `EnsureAdmin`
- Artisan-Command: `app:fetch-match-results`
- Migrationen fuer Spiele, Tipps und `is_admin`
- Blade-Views und Layout
- Demo Seeder

## Produktiv-Hinweise

- Registrierung idealerweise auf Firmen-Sign-in oder erlaubte E-Mail-Domain begrenzen.
- Datenschutzhinweis / Teilnahmebedingungen intern verlinken.
- Preise, Auslosungsmodus und Gleichstand-Regeln vor Start kommunizieren.
- Fuer hoehere Sicherheit Admin-Rollen langfristig ueber Policies oder ein Rollenpaket wie `spatie/laravel-permission` abbilden.
