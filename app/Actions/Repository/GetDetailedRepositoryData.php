<?php


namespace App\Actions\Repository;

use App\Actions\Issue\GetIssuesByName;

class GetDetailedRepositoryData
{
    public static function get($repository): void
    {
        [$githubUser, $repositoryName] = explode('/', $repository->title);
        $issues = GetIssuesByName::get($githubUser, $repositoryName, $repository->github_installation_id, 'open');

        $repository->favorite = $repository->userFavorite->isNotEmpty();
        $repository->open_issues_count = count($issues);
        $repository->issues_donations_sum_net_amount = array_reduce($issues, function ($sum, $issue) {
            return $sum + ($issue->donations_sum_net_amount ?? 0);
        }, 0);
    }
}
