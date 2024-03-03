<?php

namespace App\Http\Controllers;

use App\Actions\Issue\GetIssueById;
use App\Models\Campaign;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CampaignController extends Controller
{
    // public function store(Request $request)
    // {
    //     return CreateNewIssue::create($request->all());
    // }

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
        // Validate the incoming request data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'template' => 'required|string',
            'is_enabled' => 'boolean',
            'is_recurring_for_new_users' => 'boolean',
            'new_user_delay_days' => '',
            'start_time' => '',
        ]);

        // Update the campaign with the validated data
        $campaign->update($validatedData);
        return response()->json(['message' => 'Campaign updated successfully']);
    }

    // public function update(Request $request, $id)
    // {
    //     // Logic to update a specific resource based on the form submission
    // }
}