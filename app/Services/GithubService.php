<?php

namespace App\Services;
use App\Actions\Github\AppActions;
use App\Actions\Github\IssueActions;
use App\Actions\Github\RepositoryActions;
use App\Actions\Github\UserActions;

class GithubService
{
    const BASE_URL = 'https://api.github.com';

    public static function getRepositoryByName($githubUser, $repositoryName)
    {
        return RepositoryActions::getByName($githubUser, $repositoryName);
    }

    public static function handleGithubAppCallback($request)
    {
        return AppActions::handleCallback($request);
    }

    public static function handleGithubAppWebhook($request)
    {
        return AppActions::handleWebhook($request);
    }

    public static function getUserByGithubId($github_id)
    {
        return UserActions::getByGithubId($github_id);
    }

    public static function getRepositoriesByInstallationId($installationId, $accessToken)
    {
        return RepositoryActions::getByInstallationId($installationId, $accessToken);
    }

    public static function getRepositoryProgrammingLanguages($repositoryTitle, $accessToken)
    {
        return RepositoryActions::getProgrammingLanguages($repositoryTitle, $accessToken);
    }

    public static function getRepositoriesBySearchQuery($searchQuery, $resultsToFetch, $localResults)
    {
        return RepositoryActions::getBySearchQuery($searchQuery, $resultsToFetch, $localResults);
    }

    public static function commentOnIssue($installationId, $owner, $repo, $issueNumber, $comment)
    {
        return IssueActions::comment($installationId, $owner, $repo, $issueNumber, $comment);
    }

    public static function getIssueActivityTimeline($issueGithubUrl, $githubAccessToken, $donations)
    {
        return IssueActions::getActivityTimeline($issueGithubUrl, $githubAccessToken, $donations);
    }

    public static function getConnectedIssuesInBatch($neededIssues, $existingIssues)
    {
        return IssueActions::getConnectedInBatch($neededIssues, $existingIssues);
    }

    public static function getIssuesBySearchQuery($searchQuery, $resultsToFetch, $localResults)
    {
        return IssueActions::getBySearchQuery($searchQuery, $resultsToFetch, $localResults);
    }
}