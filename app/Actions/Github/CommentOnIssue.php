<?php

namespace App\Actions\Github;

use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CommentOnIssue
{
    public static function run($token, $owner, $repo, $issueNumber, $comment)
    {
        $url = "https://api.github.com/repos/{$owner}/{$repo}/issues/{$issueNumber}/comments";

        $response = Http::withHeaders([
            'Authorization' => "token {$token}",
            'Accept' => 'application/vnd.github+json',
        ])->post($url, [
            'body' => $comment,
        ]);

        if ($response->successful()) {
            return $response->json();
        } else {
            Log::error('Failed to post comment on GitHub issue: ', ['response' => $response->body()]);
        }
    }
    public static function constructPledgeComment($amount, $donorName, $issueId)
    {
        $appUrl = env('APP_URL');
        $issueLink = "{$appUrl}/issues/{$issueId}";
    
        $comment = view('comments.new_pledge', [
            'amount' => $amount,
            'donorName' => $donorName,
            'issueLink' => $issueLink
        ])->render();
    
        return $comment;
    }

    public static function getInstallationAccessToken($installationId)
    {
        $jwtToken = self::generateJwtToken();

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$jwtToken}",
            'Accept' => 'application/vnd.github.v3+json',
        ])->post("https://api.github.com/app/installations/{$installationId}/access_tokens");

        if ($response->successful()) {
            return $response['token'];
        } else {
            throw new \Exception('Failed to get GitHub installation access token: ' . $response->body());
        }
    }

    public static function generateJwtToken()
    {
        $privateKey = env('GITHUB_APP_PRIVATE_KEY');
        $payload = [
            'iat' => time(),
            'exp' => time() + (10 * 60), // 10 minutes expiration
            'iss' => env('GITHUB_APP_CLIENT_ID'),
        ];

        return JWT::encode($payload, $privateKey, 'RS256');
    }
}
