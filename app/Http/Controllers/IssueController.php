<?php

namespace App\Http\Controllers;

use App\Actions\Issue\CreateNewIssue;
use App\Actions\Issue\GetIssueById;
use App\Actions\Issue\GetIssues;
use App\Models\Issue;
use Illuminate\Http\Request;
use Inertia\Inertia;

class IssueController extends Controller
{
    public function store(Request $request)
    {
        return CreateNewIssue::create($request->all());
    }

    public function pledgeExternalIssue(Request $request)
    {
        $issue = Issue::where('github_id', $request->input('github_id'))->first();

        if (!isset($issue)) {
            $issue = CreateNewIssue::create($request->all());
        }
        
        return redirect()->route('issues.show', ['issue' => $issue]);
    }

    public function show($id)
    {
        $issue = GetIssueById::get($id);
        return Inertia::render('Issues/Show', [
            'issue' => $issue,
            'stripePublicKey' => config('app.stripe_key')
        ]);
    }

    public function donations($id)
    {
        $issue = GetIssueById::get($id);
        return $issue->donations;
    }
}