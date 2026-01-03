<?php

namespace App\Console\Commands;

use App\Services\InteractionService;
use Illuminate\Console\Command;

class SyncDrugInteractions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'interactions:import-standard';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import standard drug interactions from local curated database';

    /**
     * Execute the console command.
     */
    public function handle(InteractionService $service)
    {
        $this->info("Starting Import of Standard Interactions...");

        $count = $service->importStandardInteractions();

        $this->info("Done. Processed/Verified $count interaction pairs.");
    }
}
