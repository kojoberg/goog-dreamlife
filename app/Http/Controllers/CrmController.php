<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessCampaign;
use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CrmController extends Controller
{
    /**
     * Dashboard with Graphs.
     */
    public function index()
    {
        // Recent Campaigns
        $campaigns = Campaign::with('creator')->latest()->take(10)->get();

        // Stats for Delivery Report (Last 30 days or all time)
        // Aggregating locally or via query
        // For simpler implementation, we'll grab aggregate counts from campaign_recipients

        $totalSent = \App\Models\CampaignRecipient::where('status', 'sent')->count();
        $totalFailed = \App\Models\CampaignRecipient::where('status', 'failed')->count();
        $totalPending = \App\Models\CampaignRecipient::where('status', 'pending')->count();

        // Maybe grouped by date for a line chart?
        // Let's pass simple stats first.
        $deliveryStats = [
            'sent' => $totalSent,
            'failed' => $totalFailed,
            'pending' => $totalPending
        ];

        return view('admin.crm.index', compact('campaigns', 'deliveryStats'));
    }

    /**
     * Create Campaign Form.
     */
    public function create()
    {
        return view('admin.crm.create');
    }

    /**
     * Store and Dispatch Campaign.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:sms,email',
            'target_role' => 'required|string', // all, all_patients, pharmacist, etc.
            'message' => 'required|string',
        ]);

        $campaign = Campaign::create([
            'title' => $request->title,
            'type' => $request->type,
            'message' => $request->message,
            'filters' => ['role' => $request->target_role],
            'status' => 'pending',
            'created_by' => Auth::id(),
            'is_personalized' => $request->has('is_personalized'),
        ]);

        // Dispatch Job
        ProcessCampaign::dispatch($campaign);

        return redirect()->route('admin.crm.index')->with('success', 'Campaign created. Messages are being processed in the background.');
    }

    public function show(Campaign $campaign)
    {
        $campaign->load('recipients');
        return view('admin.crm.show', compact('campaign'));
    }
}
