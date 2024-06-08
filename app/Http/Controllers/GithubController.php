<?php

namespace App\Http\Controllers;

use App\Actions\Github\GetGithubIssues;
use App\Actions\Github\GetGithubRepositories;
use App\Actions\Github\HandleGithubAppCallback;
use App\Actions\Github\HandleGithubAppWebhook;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Laravel\Socialite\Facades\Socialite;

class GithubController extends Controller
{

    public function redirect(Request $request)
    {
        return Socialite::driver('github')->redirect();
    }

    public function callback(Request $request)
    {
        $user = Socialite::driver('github')->user();
        
        $dbUser = User::where('github_id', $user->id)->first();

        if (!$dbUser) {
            $avatar = file_get_contents($user->avatar);

            $fileName = 'profile-' . $user->id . '.jpg';
            Storage::put('profile-photos/' . $fileName, $avatar);

            $name = $user->name ? $user->name : explode('@', $user->email)[0];

            $dbUser = User::create([
                'name' => $name,
                'email' => $user->email,
                'github_id' => $user->id,
                'auth_type' => 'github',
                'profile_photo_path' => 'profile-photos/' . $fileName
            ]);
        }

        Auth::login($dbUser);
        return redirect('/');
    }

    public function handleGithubAppCallback(Request $request)
    {
        return HandleGithubAppCallback::run($request);
    }

    public function handleGithubAppWebhook(Request $request)
    {
        return HandleGithubAppWebhook::run($request);
    }

    public function getRepositories(Request $request)
    {
        return GetGithubRepositories::run($request->get('q'));
    }

    public function getIssues(Request $request)
    {
        return GetGithubIssues::run($request->get('q'));
    }
}
