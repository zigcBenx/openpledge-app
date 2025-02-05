<?php

namespace App\Services;

use App\Actions\Github\{
    AppActions,
    AuthActions,
    GraphQLActions,
    IssueActions,
    PullRequestActions,
    RepositoryActions,
    UserActions
};

class GithubService
{
    public const BASE_URL = 'https://api.github.com';

    public static function executeGraphQLQuery($accessToken, $query, $variables)
    {
        return GraphQLActions::executeQuery($accessToken, $query, $variables);
    }

    public static function getRepositoryByName($githubUser, $repositoryName)
    {
        return RepositoryActions::getByName($githubUser, $repositoryName);
    }

    public static function handleGithubAuthCallback()
    {
        return AuthActions::handleAuthCallback();
    }

    public static function handleGithubAuthRedirect()
    {
        return AuthActions::handleAuthRedirect();
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

    public static function getIssueActivityTimeline($issueGithubUrl, $githubAccessToken, $donations, $resolver, $resolvedAt)
    {
        return IssueActions::getActivityTimeline($issueGithubUrl, $githubAccessToken, $donations, $resolver, $resolvedAt);
    }

    public static function getConnectedIssuesInBatch($neededIssues, $existingIssues)
    {
        return IssueActions::getConnectedInBatch($neededIssues, $existingIssues);
    }

    public static function getIssuesBySearchQuery($searchQuery, $resultsToFetch, $localResults)
    {
        return IssueActions::getBySearchQuery($searchQuery, $resultsToFetch, $localResults);
    }

    public static function getPullRequestData($repositoryOwner, $repositoryName, $pullRequestNumber)
    {
        return PullRequestActions::getPullRequestData($repositoryOwner, $repositoryName, $pullRequestNumber);
    }

    public static function getConnectedPullRequests($repositoryOwner, $repositoryName, $githubIssueNumber)
    {
        return PullRequestActions::getConnectedPullRequests($repositoryOwner, $repositoryName, $githubIssueNumber);
    }
}
