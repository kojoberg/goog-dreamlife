<?php

namespace App\Console\Commands;

use App\Models\InventoryBatch;
use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Services\SmsService;

class CheckExpiry extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inventory:check-expiry';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for inventory items nearing expiry and send alerts';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $settings = Setting::first();
        if (!$settings)
            return;

        $days = $settings->alert_expiry_days ?? 90;
        $expiryDate = now()->addDays($days);

        // Find batches expiring soon (and not already 0 quantity)
        $expiringBatches = InventoryBatch::where('quantity', '>', 0)
            ->where('expiry_date', '<=', $expiryDate)
            ->where('expiry_date', '>=', now()) // Not already expired (optional, maybe we want those too?)
            ->with('product')
            ->get();

        if ($expiringBatches->isEmpty()) {
            $this->info("No expiring items found within {$days} days.");
            return;
        }

        $this->info("Found " . $expiringBatches->count() . " expiring items.");

        // Construct Message
        $messageLines = [];
        foreach ($expiringBatches as $batch) {
            $messageLines[] = "- {$batch->product->name} (Qty: {$batch->quantity}) expires on {$batch->expiry_date->format('Y-m-d')}";
        }
        $msgBody = implode("\n", $messageLines);

        // Email Alert
        if ($settings->notify_expiry_email && $settings->email) {
            try {
                Mail::raw("Expiry Alert ({$days} Days):\n\n" . $msgBody, function ($msg) use ($settings) {
                    $msg->to($settings->email)->subject('Inventory Expiry Alert');
                });
                $this->info("Email sent.");
            } catch (\Exception $e) {
                $this->error("Email failed: " . $e->getMessage());
            }
        }

        // SMS Alert
        if ($settings->notify_expiry_sms && $settings->phone) {
            try {
                // Truncate for SMS if too long
                $smsBody = "Expiry Alert! " . $expiringBatches->count() . " items exp soon. Check Dashboard.";
                $sms = new SmsService();
                $sms->sendQuickSms($settings->phone, $smsBody);
                $this->info("SMS sent.");
            } catch (\Exception $e) {
                $this->error("SMS failed: " . $e->getMessage());
            }
        }
    }
}
