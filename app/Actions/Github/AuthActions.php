<?php

namespace App\Actions\Github;

use App\Models\GitHubInstallation;
use App\Models\Repository;
use App\Services\GithubService;
use Firebase\JWT\JWT;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Laravel\Socialite\Facades\Socialite;

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

    public static function handleAuthRedirect()
    {
        return Socialite::driver('github')
            ->scopes(['read:user', 'repo', 'read:org'])
            ->redirect();
    }

    public static function handleAuthCallback()
    {
        $githubAccountData = Socialite::driver('github')->user();

        $authenticatedUser = Auth::user();

        if ($authenticatedUser) {
            // We sync the github id to the authenticated user
            $authenticatedUser->github_id = $githubAccountData->id;
            $authenticatedUser->save();
            Auth::login($authenticatedUser);
            $redirectPath = session('github_redirect_path');
            if ($redirectPath) {
                session()->forget('github_redirect_path');
                return redirect($redirectPath);
            }
            return redirect('/');
        }

        $dbUser = User::where('github_id', $githubAccountData->id)->first();

        if ($dbUser) {
            if (! $dbUser->github_token) {
                $dbUser->github_token = $githubAccountData->token;
                $dbUser->save();
            }
            // User is already connected with GitHub
            Auth::login($dbUser);
            return redirect('/');
        }

        $userWithSameEmail = User::where('email', $githubAccountData->email)->first();

        if ($userWithSameEmail) {
            // There is an account with the same email as the github user
            if ($userWithSameEmail->email_verified_at) {
                // The account has verified its email, so we can sync the github id without any security concerns
                $userWithSameEmail->github_id = $githubAccountData->id;
                $userWithSameEmail->save();
                Auth::login($userWithSameEmail);
                return redirect('/');
            }
            // For security reasons, if email is not verified, we should not do anything, since we can't verify if this user is the owner of the email
            // TODO: Force users to verify their email if they sign up manually
            return Inertia::render('Error', [
                'message' => 'Account with this email already exists.',
                'subMessage' => 'Please contact support from <b>' . $githubAccountData->email . '</b> to verify ownership of this email.',
                'redirectUrl' => route('login'),
                'redirectButtonText' => 'Retry logging in'
            ]);
        }

        // User is new to the platform, we create a new account
        $avatar = file_get_contents($githubAccountData->avatar);

        $fileName = 'profile-' . $githubAccountData->id . '.jpg';
        Storage::put('profile-photos/' . $fileName, $avatar);

        $name = $githubAccountData->name ?: explode('@', $githubAccountData->email)[0];

        try {
            $dbUser = User::create([
                'name'               => $name,
                'email'              => $githubAccountData->email,
                'github_id'          => $githubAccountData->id,
                'github_token'       => $githubAccountData->token,
                'auth_type'          => 'github',
                'profile_photo_path' => 'profile-photos/' . $fileName
            ]);
            event(new Registered($dbUser));
        } catch (\Exception $e) {
            logger('[ERROR] Failed to create user: ' . $e->getMessage(), [
                'github_account_data' => $githubAccountData,
                'stack_trace' => $e->getTraceAsString()
            ]);
            return Inertia::render('Error', [
                'message' => 'Failed to create user. Please try again.',
                'subMessage' => 'If the issue persists, please contact support.',
                'redirectUrl' => route('login'),
                'redirectButtonText' => 'Retry logging in'
            ]);
        }

        Auth::login($dbUser);
        return redirect('/');
    }
}
