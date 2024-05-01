<?php

namespace App\Http\Controllers;

use App\Actions\Github\GetGithubIssues;
use App\Actions\Github\GetGithubRepositories;
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

            // Store the image in Laravel storage
            $fileName = 'profile-' . $user->id . '.jpg'; // You can change the file name as per your preference
            Storage::put('profile_images/' . $fileName, $avatar);

            $dbUser = User::create([
                'name' => $user->name,
                'email' => $user->email,
                'github_id' => $user->id,
                'auth_type' => 'github',
                'profile_photo_path' => 'profile_images/' . $fileName
            ]);
        }

        Auth::login($dbUser);
        return redirect('/');
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
