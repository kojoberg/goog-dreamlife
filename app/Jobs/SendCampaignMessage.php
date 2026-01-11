<?php

namespace App\Jobs;

use App\Models\CampaignRecipient;
use App\Services\SmsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendCampaignMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $recipient;

    /**
     * Create a new job instance.
     */
    public function __construct(CampaignRecipient $recipient)
    {
        $this->recipient = $recipient;
    }

    /**
     * Execute the job.
     */
    public function handle(SmsService $smsService): void
    {
        $campaign = $this->recipient->campaign;

        $messageBody = $campaign->message;
        if ($campaign->is_personalized && $this->recipient->recipient) {
            $messageBody = str_replace('[Name]', $this->recipient->recipient->name, $messageBody);
        }

        try {
            if ($campaign->type === 'sms') {
                $response = $smsService->sendQuickSms($this->recipient->contact, $messageBody, 'campaign');
                $this->recipient->update(['status' => 'sent', 'sent_at' => now()]);

            } elseif ($campaign->type === 'email') {
                Mail::raw($messageBody, function ($message) use ($campaign) {
                    $message->to($this->recipient->contact)
                        ->subject($campaign->title);
                });

                // Log email to communication log
                \App\Models\CommunicationLog::create([
                    'type' => 'email',
                    'recipient' => $this->recipient->contact,
                    'message' => substr($messageBody, 0, 500), // Truncate for storage
                    'status' => 'sent',
                    'context' => 'campaign',
                    'user_id' => $campaign->user_id,
                    'branch_id' => null,
                ]);

                $this->recipient->update(['status' => 'sent', 'sent_at' => now()]);
            }
        } catch (\Exception $e) {
            Log::error("Campaign Send Failed: " . $e->getMessage());

            // Log failed email if applicable
            if ($campaign->type === 'email') {
                \App\Models\CommunicationLog::create([
                    'type' => 'email',
                    'recipient' => $this->recipient->contact,
                    'message' => substr($messageBody, 0, 500),
                    'status' => 'failed',
                    'response' => $e->getMessage(),
                    'context' => 'campaign',
                    'user_id' => $campaign->user_id,
                    'branch_id' => null,
                ]);
            }

            $this->recipient->update([
                'status' => 'failed',
                'error' => $e->getMessage()
            ]);
        }
    }
}
