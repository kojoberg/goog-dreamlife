<?php

namespace Tests\Feature;

use App\Jobs\ProcessCampaign;
use App\Models\Campaign;
use App\Models\User;
use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class CrmTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        \App\Models\Setting::create(['business_name' => 'Test']);

        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    public function test_admin_can_access_crm_dashboard()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.crm.index'));
        $response->assertStatus(200);
    }

    public function test_admin_can_create_campaign()
    {
        Queue::fake();

        $response = $this->actingAs($this->admin)->post(route('admin.crm.store'), [
            'title' => 'Test Campaign',
            'type' => 'email',
            'target_role' => 'all_users',
            'message' => 'Hello World'
        ]);

        $response->assertRedirect(route('admin.crm.index'));

        $this->assertDatabaseHas('campaigns', [
            'title' => 'Test Campaign',
            'status' => 'pending'
        ]);

        Queue::assertPushed(ProcessCampaign::class);
    }

    public function test_process_campaign_job_creates_recipients()
    {
        Queue::fake();

        // Create Users
        User::factory()->count(3)->create();

        $campaign = Campaign::create([
            'title' => 'Job Test',
            'type' => 'email',
            'message' => 'Test',
            'filters' => ['role' => 'all_users'],
            'status' => 'pending',
            'created_by' => $this->admin->id
        ]);

        // Hand dispatch job synchronously-ish or manually call handle
        // To test handle logic strictly without Queue::fake blocking it, we can instantiate the job

        // We need to NOT fake queue for this specific test if we want to run the job 
        // OR better, just call handle() manually.

        $job = new ProcessCampaign($campaign);
        $job->handle();

        // Recipients should be created (Admin + 3 new users = 4)
        $this->assertGreaterThanOrEqual(4, $campaign->recipients()->count());

        $campaign->refresh();
        $this->assertEquals('completed', $campaign->status);
    }
}
