<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FetchMatchResults extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fetch-match-results {league=wm2026}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ruft aktuelle Spielergebnisse von OpenLigaDB ab und aktualisiert Punkte.';

    /**
     * Execute the console command.
     */
    public function handle(\App\Services\MatchResultService $service)
    {
        $league = $this->argument('league');
        $this->info("Rufe Ergebnisse für {$league} ab...");

        $service->fetchAndStoreResults($league);

        $this->info('Erfolgreich aktualisiert.');
    }
}
