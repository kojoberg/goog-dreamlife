<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SendRefillReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-refill-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send SMS reminders for chronic medication refills';

    public function handle()
    {
        $this->info('Checking for due refills...');

        $dueRefills = \App\Models\RefillQueue::where('status', 'pending')
            ->whereDate('scheduled_date', '<=', now())
            ->with(['patient', 'saleItem']) // Optimize
            ->get();

        $smsService = new \App\Services\SmsService();
        $count = 0;

        foreach ($dueRefills as $refill) {
            $patient = $refill->patient;
            if (!$patient || !$patient->phone) {
                $refill->update(['status' => 'failed', 'sent_at' => now()]);
                continue;
            }

            // Send SMS with context for communication logging
            $message = "Hello {$patient->name}, your refill for {$refill->product_name} is due soon. Please visit Dream Life Pharmacy to restock.";
            $response = $smsService->sendQuickSms($patient->phone, $message, 'refill_reminder');

            if ($response['success']) {
                $refill->update(['status' => 'sent', 'sent_at' => now()]);
                $count++;
            } else {
                $refill->update(['status' => 'failed', 'sent_at' => now()]);
                $this->error("Failed to send to {$patient->name}: " . ($response['message'] ?? 'Unknown error'));
            }
        }

        $this->info("Sent {$count} reminders.");
    }
}
