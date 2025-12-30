<?php

namespace Tests\Feature;

use App\Jobs\SendCampaignMessage;
use App\Models\Campaign;
use App\Models\CampaignRecipient;
use App\Models\User;
use App\Services\SmsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class PersonalizedCrmTest extends TestCase
{
    use RefreshDatabase;

    public function test_send_job_replaces_name_placeholder_when_personalized()
    {
        // 1. Setup
        $user = User::factory()->create(['name' => 'John Doe', 'email' => 'john@example.com']);

        $campaign = Campaign::create([
            'title' => 'Personalized Test',
            'type' => 'sms',
            'status' => 'processing',
            'created_by' => $user->id,
            'message' => 'Hello [Name], welcome!',
            'filters' => [],
            'is_personalized' => true,
        ]);

        $recipient = CampaignRecipient::create([
            'campaign_id' => $campaign->id,
            'recipient_type' => User::class,
            'recipient_id' => $user->id,
            'contact' => '1234567890',
            'status' => 'pending'
        ]);

        // 2. Mock SmsService
        $mockSms = Mockery::mock(SmsService::class);
        $mockSms->shouldReceive('sendQuickSms')
            ->once()
            ->with('1234567890', 'Hello John Doe, welcome!')
            ->andReturn(['success' => true]);

        // 3. Run Job
        $job = new SendCampaignMessage($recipient);
        $job->handle($mockSms);

        // 4. Assert Recipient Status Updated
        $recipient->refresh();
        if ($recipient->status === 'failed') {
            dump($recipient->error);
        }
        $this->assertEquals('sent', $recipient->status);
    }

    public function test_send_job_does_not_replace_name_if_not_personalized()
    {
        // 1. Setup
        $user = User::factory()->create(['name' => 'John Doe']);

        $campaign = Campaign::create([
            'title' => 'Generic Test',
            'type' => 'sms',
            'status' => 'processing',
            'created_by' => $user->id,
            'message' => 'Hello [Name], welcome!',
            'filters' => [],
            'is_personalized' => false, // disabled
        ]);

        $recipient = CampaignRecipient::create([
            'campaign_id' => $campaign->id,
            'recipient_type' => User::class,
            'recipient_id' => $user->id,
            'contact' => '1234567890',
            'status' => 'pending'
        ]);

        // 2. Mock SmsService
        $mockSms = Mockery::mock(SmsService::class);
        $mockSms->shouldReceive('sendQuickSms')
            ->once()
            ->with('1234567890', 'Hello [Name], welcome!') // Should NOT replace
            ->andReturn(['success' => true]);

        // 3. Run Job
        $job = new SendCampaignMessage($recipient);
        $job->handle($mockSms);

        $recipient->refresh();
        $this->assertEquals('sent', $recipient->status);
    }
}
