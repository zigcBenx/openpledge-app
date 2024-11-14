<?php

namespace App\Http\Controllers;

use App\Models\ProgrammingLanguage;
use App\Services\GithubService;
use App\Actions\Issue\GetIssues;
use App\Models\Donation;
use App\Models\Issue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class MainController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Home');
    }

    public function dashboard(Request $request)
    {
        return Inertia::render('Dashboard');
    }

    public function discoverIssues(Request $request)
    {
        $user = Auth::user();

        $filters = $request->query('filters', []);
        $existingUrls = $request->query('existingUrls', []);
        $page = $request->query('page', 1);
        $perPage = 10;
        $offset = ($page - 1) * $perPage;

        $pledgedIssues = GetIssues::getWithActiveDonations($filters, $offset, $perPage);

        $programmingLanguages = ProgrammingLanguage::select('id', 'name')->get();

        // Immediately return if filters are present
        if (!empty($filters)) {
            return Inertia::render('Discover/Issues', [
                'issues' => $pledgedIssues,
                'userIsContributor' => $user->isContributor(),
                'userIsResolver' => $user->isResolver(),
                'programmingLanguages' => $programmingLanguages
            ]);
        }

        if (empty($existingUrls)) {
            $existingUrls = array_map(function ($issue) {
                return $issue['github_url'];
            }, $pledgedIssues->toArray());
        }

        if (count($pledgedIssues) < $perPage) {
            $neededIssues = $perPage - count($pledgedIssues);

            $externalIssues = GithubService::getConnectedIssuesInBatch($neededIssues, $existingUrls);
            $combinedIssues = array_merge($pledgedIssues->toArray(), $externalIssues);
        }

        $paginatedIssues = array_slice($combinedIssues, 0, $perPage);

        // For the first page, use Inertia to render the page
        if ($page === 1) {
            return Inertia::render('Discover/Issues', [
                'issues' => $paginatedIssues,
                'userIsContributor' => $user->isContributor(),
                'userIsResolver' => $user->isResolver(),
                'programmingLanguages' => $programmingLanguages
            ]);
        }

        // If the initial page is already loaded, return a JSON response that will combine already displayed issues with freshly queried issues
        return response()->json([
            'issues' => $paginatedIssues,
        ]);
    }

    public function getTopContributors()
    {
        $topResolvers = Issue::where('state', 'closed')
            ->whereNotNull('resolver_github_id')
            ->selectRaw('resolver_github_id, COUNT(*) as issue_count')
            ->groupBy('resolver_github_id')
            ->orderByDesc('issue_count')
            ->limit(5)
            ->get();

        $githubUsers = [];
        foreach ($topResolvers as $resolver) {
            $githubUser = GithubService::getUserByGithubId($resolver->resolver_github_id);
            $githubUser['issueCount'] = $resolver->issue_count;
            $githubUsers[] = $githubUser;
        }

        return $githubUsers;
    }

    public function getTopDonors()
    {
        return Donation::select('donor_id', DB::raw('SUM(amount) as total_donated'))
            ->groupBy('donor_id')
            ->orderByDesc('total_donated')
            ->with('user')
            ->take(5)
            ->get();
    }
}
