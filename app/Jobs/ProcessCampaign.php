<?php

namespace App\Jobs;

use App\Models\Campaign;
use App\Models\User;
use App\Models\Patient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessCampaign implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $campaign;

    /**
     * Create a new job instance.
     */
    public function __construct(Campaign $campaign)
    {
        $this->campaign = $campaign;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->campaign->update(['status' => 'processing']);

        Log::info("Processing Campaign ID: {$this->campaign->id}");

        $filters = $this->campaign->filters; // ['role' => 'patient'] etc.
        $role = $filters['role'] ?? 'all';

        $recipients = collect();

        if ($role === 'patient' || $role === 'all_patients') {
            $recipients = $recipients->merge(Patient::all());
        } elseif ($role === 'all_users') {
            $recipients = $recipients->merge(User::all());
        } else {
            // Specific roles: pharmacist, cashier, admin
            // Assuming User model has 'role' column.
            if ($role !== 'all') {
                $recipients = $recipients->merge(User::where('role', $role)->get());
            }
        }

        foreach ($recipients as $recipient) {
            $contact = null;

            if ($this->campaign->type === 'sms') {
                $contact = $recipient->phone;
            } else {
                $contact = $recipient->email;
            }

            if ($contact) {
                $campaignRecipient = $this->campaign->recipients()->create([
                    'recipient_type' => get_class($recipient),
                    'recipient_id' => $recipient->id,
                    'contact' => $contact,
                    'status' => 'pending'
                ]);

                // Dispatch Sending Job
                SendCampaignMessage::dispatch($campaignRecipient);
            }
        }

        $this->campaign->update(['status' => 'completed']);
    }
}
