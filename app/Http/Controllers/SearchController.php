<?php
namespace App\Http\Controllers;

use App\Actions\Search\SearchIssues;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Actions\Search\SearchRepositories;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    public function getSearchResults(Request $request)
    {
        $searchQuery = $request->input('query');
        $isAuthenticated = Auth::check();
        $includeGitHubResults = $isAuthenticated && filter_var($request->input('includeGitHub'), FILTER_VALIDATE_BOOLEAN);
        $totalResultsLimit = 5;

        $repositoryResults = SearchRepositories::search($totalResultsLimit, $searchQuery, $includeGitHubResults);
        $issueResults = SearchIssues::search($totalResultsLimit, $searchQuery, $includeGitHubResults);

        return [
            "repositories" => $repositoryResults,
            "issues" => $issueResults
        ];
    }
}