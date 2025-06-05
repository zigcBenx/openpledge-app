<?php

namespace App\Http\Controllers;

use App\Actions\Donation\Donators;
use App\Actions\Donation\GetDonations;
use App\Actions\User\Contributors;
use App\Models\ProgrammingLanguage;
use App\Services\GithubService;
use App\Actions\Issue\GetIssues;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
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

    public function saveRedirectPath(Request $request)
    {
        $redirectPath = $request->input('redirect_path');
        $redirectPathKey = $request->input('redirect_path_key');
        session([$redirectPathKey => $redirectPath]);
        return response()->json(['success' => true, 'message' => 'Redirect path saved successfully!']);
    }

    private function getPaginationParameters(Request $request)
    {
        $filters = $request->query('filters', []);
        $existingUrls = $request->query('existingUrls', []);
        $page = (int) $request->query('page', 1);
        $perPage = 10;
        $offset = ($page - 1) * $perPage;

        return compact('filters', 'existingUrls', 'page', 'perPage', 'offset');
    }

    public function discoverIssues(Request $request)
    {
        $user = Auth::user();
        $paginationParams = $this->getPaginationParameters($request);

        $pledgedIssues = GetIssues::getWithActiveDonations(
            $paginationParams['filters'], 
            $paginationParams['offset'], 
            $paginationParams['perPage']
        );

        $programmingLanguages = ProgrammingLanguage::select('id', 'name')->get();
        $showPledgedOnly = filter_var($paginationParams['filters']['showPledgedOnly'] ?? false, FILTER_VALIDATE_BOOLEAN);

        if (empty($paginationParams['existingUrls'])) {
            $paginationParams['existingUrls'] = array_map(function ($issue) {
                return $issue['github_url'];
            }, $pledgedIssues->toArray());
        }

        if (count($pledgedIssues) < $paginationParams['perPage']) {
            $neededIssues = $paginationParams['perPage'] - count($pledgedIssues);
            $externalIssues = [];

            if (!$showPledgedOnly) {
                $externalIssues = GithubService::getConnectedIssuesInBatch($neededIssues, $paginationParams['existingUrls'], $paginationParams['filters']);
            }

            $combinedIssues = array_merge($pledgedIssues->toArray(), $externalIssues);
        }

        $paginatedIssues = array_slice($combinedIssues ?? $pledgedIssues->toArray(), 0, $paginationParams['perPage']);

        // For the first page, use Inertia to render the page
        if ($paginationParams['page'] === 1) {
            return Inertia::render('Discover/Issues', [
                'issues' => $paginatedIssues,
                'userIsContributor' => isset($user) ? $user->isContributor() : true,
                'userIsResolver' => isset($user) ? $user->isResolver() : true,
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
        return Contributors::getTopContributors();
    }

    public function getTopDonors()
    {
        return Donators::getTopDonors();
    }

    public function getAnonymousDonations()
    {
        return GetDonations::getAnonymous();
    }
}
