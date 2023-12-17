<?php

namespace App\Actions\Issue;

use App\Models\Issue;
use Illuminate\Support\Facades\Validator;

class CreateNewIssue
{
    public static function create(array $input): Issue
    {            
        Validator::make($input, [
            'title'         => ['required', 'string', 'max:255'],
            'github_url'    => ['required', 'string', 'url',],
            'github_id'     => ['required','unique:issues,github_id'],
            'repository_id' => ['required','exists:repositories,id'],
            'user_avatar'   => ['string'],
        ])->validate();

        return Issue::create([
            'title'         => $input['title'],
            'github_url'    => $input['github_url'],
            'github_id'     => $input['github_id'],
            'repository_id' => $input['repository_id'],
            'user_avatar'   => $input['user_avatar'],
        ]);
    }
}
