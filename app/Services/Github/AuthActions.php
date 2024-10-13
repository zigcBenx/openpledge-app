<?php

namespace App\Services\Github;

use App\Models\GitHubInstallation;
use App\Models\Repository;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Http;

class AuthActions
{
    private static function generateJwtToken()
    {
        $privateKey = env('GITHUB_APP_PRIVATE_KEY');
        $payload = [
            'iat' => time(),
            'exp' => time() + 10 * 60, // 10 minutes expiration
            'iss' => env('GITHUB_APP_CLIENT_ID'),
        ];

        return JWT::encode($payload, $privateKey, 'RS256');
    }

    public static function getAccessTokenByAuthenticatedUser($authenticatedUser)
    {
        $authenticatedUserToken = $authenticatedUser->getGitHubAccessToken();

        if ($authenticatedUserToken) {
            return $authenticatedUserToken;
        }

        $randomInstallation = GitHubInstallation::inRandomOrder()->first();

        if ($randomInstallation && !empty($randomInstallation->access_token)) {
            return $randomInstallation->access_token;
        }

        return null;
    }

    public static function getAccessTokenByRepositoryUrl($repositoryUrl)
    {
        $repository = Repository::with('githubInstallation')
            ->where('github_url', $repositoryUrl)
            ->first();

        if (!$repository) {
            logger('[ERROR] Repository or GitHub installation not found', ['repo_url' => $repositoryUrl]);
            throw new \Exception("Repository or GitHub installation not found");
        }

        $accessToken = optional($repository->githubInstallation)->access_token;

        if (!$accessToken) {
            logger('[ERROR] Access token not found', ['repository' => $repository]);
            throw new \Exception("Access token not found");
        }

        return $accessToken;
    }

    public static function generateAppInstallationAccessToken($installationId)
    {
        $jwtToken = AuthActions::generateJwtToken();
        $url = GithubService::BASE_URL . "/app/installations/{$installationId}/access_tokens";

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$jwtToken}",
            'Accept' => 'application/vnd.github.v3+json',
        ])->post($url);

        if ($response->successful()) {
            return $response['token'];
        } else {
            throw new \Exception('Failed to get GitHub installation access token: ' . $response->body());
        }
    }
}