<?php

namespace App\Http\Controllers;

use App\Services\GithubService;
use App\Actions\Issue\CreateNewIssue;
use App\Actions\Issue\GetIssueById;
use App\Actions\Issue\SolveIssue;
use App\Http\Requests\CreateNewIssueRequest;
use App\Models\Issue;
use App\Models\Label;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Inertia\Inertia;

class IssueController extends Controller
{
    public function store(CreateNewIssueRequest $request)
    {
        return CreateNewIssue::create($request->validated());
    }

    public function pledgeExternalIssue(Request $request)
    {
        $issue = Issue::where('github_id', $request->input('github_id'))->first();

        if (!isset($issue)) {
            $validatedIssueData = app(CreateNewIssueRequest::class)->validated();
            $issue = CreateNewIssue::create($validatedIssueData);
            $labels = $request->input('labels');

            foreach ($labels as $label) {
                if (in_array(strtolower($label['name']), Label::$allowedLabels)) {
                    $issue->labels()->create([
                        'name' => strtolower($label['name'])
                    ]);
                }
            }
        }

        return redirect()->route('issues.show', ['issue' => $issue]);
    }

    public function show($id)
    {
        $issue = GetIssueById::get($id);
        $issue->issueResolver = GithubService::getUserByGithubId($issue->resolver_github_id);
        $issue->issueActivity = GithubService::getIssueActivityTimeline(
            $issue->github_url,
            $issue->repository->githubInstallation->access_token,
            $issue->donations,
            $issue->issueResolver,
            $issue->resolved_at
        );

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
            $issue->today_donations_sum = $issue->donations->sum('net_amount');
            return $issue;
        })
        ->sortByDesc('today_donations_sum')
        ->take(5);
    }
}
