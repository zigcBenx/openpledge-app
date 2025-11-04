<?php

namespace App\Actions\Github;

use App\Services\GithubService;
use Illuminate\Support\Facades\Http;

class LabelActions
{
    public static function createPledgeableLabel($installationId, $owner, $repo)
    {
        $url = GithubService::BASE_URL . "/repos/{$owner}/{$repo}/labels";
        $token = AuthActions::generateAppInstallationAccessToken($installationId);

        $response = Http::withHeaders([
            'Authorization' => "token {$token}",
            'Accept' => 'application/vnd.github+json',
        ])->post($url, [
            'name' => 'Pledgeable',
            'description' => 'This issue can receive pledges/funds on OpenPledge',
            'color' => '10B981'
        ]);

        if ($response->successful()) {
            return $response->json();
        } else {
            if ($response->status() === 422) {
                $errorData = $response->json();
                if (isset($errorData['errors']) &&
                    collect($errorData['errors'])->contains('field', 'name')) {
                    return ['message' => 'Label already exists'];
                }
            }

            logger('[ERROR] Failed to create Pledgeable label on GitHub: ', [
                'response' => $response->body(),
                'status' => $response->status()
            ]);

            throw new \Exception('Failed to create Pledgeable label: ' . $response->body());
        }
    }

    public static function checkLabelExists($installationId, $owner, $repo, $labelName = 'Pledgeable')
    {
        $url = GithubService::BASE_URL . "/repos/{$owner}/{$repo}/labels/{$labelName}";
        $token = AuthActions::generateAppInstallationAccessToken($installationId);

        $response = Http::withHeaders([
            'Authorization' => "token {$token}",
            'Accept' => 'application/vnd.github+json',
        ])->get($url);

        return $response->successful();
    }
}
