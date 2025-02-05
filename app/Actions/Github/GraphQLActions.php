<?php

namespace App\Actions\Github;

use Exception;
use Illuminate\Support\Facades\Http;
use App\Services\GithubService;

class GraphQLActions
{
    public static function executeQuery($accessToken, $query, $variables)
    {
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$accessToken}",
            'Content-Type' => 'application/json',
        ])->post(GithubService::BASE_URL . '/graphql', [
                    'query' => $query,
                    'variables' => $variables,
                ]);

        if ($response->successful()) {
            return $response->json();
        }

        throw new Exception('Failed to execute GitHub GraphQL query, response: ' . $response->body());
    }
}