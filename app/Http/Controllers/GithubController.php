<?php

namespace App\Http\Controllers;

use App\Actions\Github\GetGithubRepositories;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            $dbUser = User::create([
                'name' => $user->name,
                'email' => $user->email,
                'github_id' => $user->id,
                'auth_type' => 'github',
            ]);
        }

        Auth::login($dbUser);
        return redirect('/');
    }

    public function getRepositories(Request $request)
    {
        return GetGithubRepositories::run($request->get('q'));
    }
}
