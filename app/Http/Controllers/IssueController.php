<?php

namespace App\Http\Controllers;

use App\Actions\Github\GetGithubUser;
use App\Actions\Issue\CreateNewIssue;
use App\Actions\Issue\GetIssueActivity;
use App\Actions\Issue\GetIssueById;
use App\Actions\Issue\SolveIssue;
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
        $issue->issueResolver = GetGithubUser::getByGithubId($issue->resolver_github_id);
        $issue->issueActivity = GetIssueActivity::get($issue->github_url, $issue->repository->githubInstallation->access_token, $issue->donations);

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

    public function solve(Request $request)
    {
        return SolveIssue::solve($request->input('issue_id'));
    }

    public function getTrendingToday()
    {
        return Issue::where('state', 'open')
        ->whereHas('donations', function ($query) {
            $query->whereDate('created_at', now()->toDateString());
        })
        ->with('donations', 'repository:id,title')
        ->get()
        ->map(function($issue) {
            $issue->today_donations_sum = $issue->donations->sum('amount');
            return $issue;
        })
        ->sortByDesc('today_donations_sum')
        ->take(5);
    }
}