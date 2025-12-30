<?php

namespace App\Jobs;

use App\Models\Product;
use App\Services\DrugInteractionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncDrugInteractionsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $product;

    /**
     * Create a new job instance.
     *
     * @param Product $product
     * @return void
     */
    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    /**
     * Execute the job.
     *
     * @param DrugInteractionService $service
     * @return void
     */
    public function handle(DrugInteractionService $service)
    {
        try {
            Log::info("Job processing interaction sync for product: {$this->product->name}");
            $service->processProduct($this->product);
        } catch (\Exception $e) {
            Log::error("Job failed to sync interactions for {$this->product->name}: " . $e->getMessage());
            $this->fail($e);
        }
    }
}
