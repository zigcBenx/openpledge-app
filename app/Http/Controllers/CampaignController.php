<?php

namespace App\Http\Controllers;

use App\Actions\Issue\GetIssueById;
use App\Models\Campaign;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Inertia\Inertia;

class CampaignController extends Controller
{

    public function index()
    {
        $campaigns = Campaign::all();// TODO: move to actions
        return Inertia::render('Campaigns/Index', [
            'campaigns' => $campaigns
        ]);
    }

    public function show($id)
    {
        $issue = GetIssueById::get($id);
        return Inertia::render('Issues/Show', [
            'issue' => $issue
        ]);
    }

    public function edit($id)
    {
        $campaign = Campaign::find($id);
        return Inertia::render('Campaigns/Edit', [
            'campaign' => $campaign
        ]);
    }

    public function update(Request $request, Campaign $campaign)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'string',
            'is_enabled' => 'boolean',
            'is_recurring_for_new_users' => 'boolean',
            'new_user_delay_days' => '',
            'start_time' => '',
        ]);

        $campaign->update($validatedData);
        return response()->json(['message' => 'Campaign updated successfully']);
    }

    public function create()
    {
        return Inertia::render('Campaigns/Create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'string',
            'is_enabled' => 'boolean',
            'is_recurring_for_new_users' => 'boolean',
            'new_user_delay_days' => '',
            'start_time' => '',
        ]);

        // Update the campaign with the validated data
        Campaign::create($validatedData);
        return response()->json(['message' => 'Campaign updated successfully']);
    }

    public function run()
    {
        try {
            Artisan::call('campaigns:run');
            return response()->json(['message' => 'Campaigns ran successfully']);
        } catch(Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
        
    }
}