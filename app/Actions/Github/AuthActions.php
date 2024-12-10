<?php

namespace App\Actions\Github;

use App\Models\GitHubInstallation;
use App\Models\Repository;
use App\Services\GithubService;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Http;

class AuthActions
{
    private static function generateJwtToken()
    {
        $privateKey = config('services.github.app_private_key');
        $payload = [
            'iat' => time(),
            'exp' => time() + 10 * 60, // 10 minutes expiration
            'iss' => config('services.github.app_client_id'),
        ];

        return JWT::encode($payload, $privateKey, 'RS256');
    }

    public static function getAccessTokenByAuthenticatedUser($authenticatedUser)
    {
        if (!isset($authenticatedUser)) {
            return AuthActions::getRandomInstallationAccessToken();
        }
        
        $authenticatedUserToken = $authenticatedUser->getGitHubAccessToken();

        if ($authenticatedUserToken) {
            return $authenticatedUserToken;
        } 
        
        return AuthActions::getRandomInstallationAccessToken();
    }

    public static function getRandomInstallationAccessToken()
    {
        $randomInstallation = GitHubInstallation::inRandomOrder()->first();

        return $randomInstallation->access_token;
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