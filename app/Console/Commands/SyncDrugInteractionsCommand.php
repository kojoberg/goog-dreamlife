<?php

namespace App\Console\Commands;

use App\Services\DrugInteractionService;
use Illuminate\Console\Command;

class SyncDrugInteractionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'drug-interactions:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch jobs to sync drug interactions from external APIs';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(DrugInteractionService $service)
    {
        $this->info('Starting drug interaction sync...');

        $count = $service->dispatchSyncJobs();

        $this->info("Dispatched sync jobs for {$count} products.");
        return 0;
    }
}
