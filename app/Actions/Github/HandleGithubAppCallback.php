<?php

namespace App\Actions\Github;

use App\Http\Requests\CreateNewRepositoryRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Models\GitHubInstallation;
use App\Actions\Repository\CreateNewRepository;

class HandleGithubAppCallback
{
    public static function run(Request $request)
    {
        DB::beginTransaction();

        try {
            $code = $request->input('code');
            $installationId = $request->input('installation_id');
            $setupAction = $request->input('setup_action');

            $clientId = config('services.github.app_client_id');
            $clientSecret = config('services.github.app_client_secret');
            $redirectUri = config('services.github.app_callback');

            $response = Http::asForm()->post('https://github.com/login/oauth/access_token', [
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'code' => $code,
                'redirect_uri' => $redirectUri,
            ]);

            if ($response->successful()) {
                $data = [];
                parse_str($response->body(), $data);

                $accessToken = $data['access_token'];

                // Fetch repositories for the specific installation
                $githubRepositoriesData = self::fetchGithubRepositories($accessToken, $installationId);

                $user = Auth::user();
                GitHubInstallation::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'installation_id' => $installationId,
                        'setup_action' => $setupAction,
                        'access_token' => $accessToken,
                    ]
                );

                foreach ($githubRepositoriesData as $repository) {
                    $validatedRepositoryData = app(CreateNewRepositoryRequest::class)->validate([
                        'title' => $repository['full_name'],
                        'github_url' => $repository['html_url'],
                        'github_id' => $repository['id'],
                        'user_avatar' => $repository['owner']['avatar_url'],
                        'user_id' => $user->id,
                        'github_installation_id' => $installationId
                    ]);
    
                    CreateNewRepository::create($validatedRepositoryData);
                }

                DB::commit();

                return redirect('/user/profile');
            } else {
                logger('[ERROR] Failed to authenticate with GitHub.', ['response' => $response->body()]);
                return redirect('/error');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            logger('[ERROR] Transaction failed: ' . $e->getMessage());
            return redirect('/error')->with('error', 'An error occurred while processing your request.');
        }
    }

    protected static function fetchGithubUser($accessToken)
    {
        $response = Http::withToken($accessToken)->get('https://api.github.com/user');

        if ($response->successful()) {
            return $response->json();
        } else {
            logger('[ERROR] Failed to fetch GitHub user data', ['response' => $response->body()]);
            return null;
        }
    }

    public static function fetchGithubRepositories($accessToken, $installationId)
    {
        $response = Http::withToken($accessToken)
            ->get("https://api.github.com/user/installations/{$installationId}/repositories");

        if ($response->successful()) {
            return $response->json()['repositories'] ?? [];
        } else {
            logger('[ERROR] Failed to fetch GitHub repositories', ['response' => $response->body()]);
            return [];
        }
    }
}
