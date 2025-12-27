<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FetchDrugInteractions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'drugs:sync-interactions';
    protected $description = 'Fetch and sync drug interactions from the NLM RxNav API';

    public function handle()
    {
        $this->info('Starting Drug Interaction Sync...');

        $service = new \App\Services\DrugInteractionService();

        $this->info('Fetching interactions for verified products...');
        $service->syncInteractions();

        $this->info('Sync completed.');
    }
}
