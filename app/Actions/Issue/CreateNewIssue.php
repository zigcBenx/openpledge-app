<?php

namespace App\Actions\Issue;

use App\Models\Issue;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class CreateNewIssue
{
    public static function create(array $input): Issue
    {            
        Validator::make($input, [
            'title'              => ['required', 'string', 'max:255'],
            'github_url'         => ['required', 'string', 'url',],
            'github_id'          => ['required','unique:issues,github_id'],
            'repository_id'      => ['required','exists:repositories,id'],
            'user_avatar'        => ['string'],
            'github_username'    => ['string'],
            'github_created_at'  => ['required', 'date'],
            'resolver_github_id' => ['string'],
            'resolved_at'        => ['date'],
            'state'              => ['required', 'string', 'max:255']
        ])->validate();

        return Issue::create([
            'title'              => $input['title'],
            'github_url'         => $input['github_url'],
            'github_id'          => $input['github_id'],
            'repository_id'      => $input['repository_id'],
            'user_avatar'        => $input['user_avatar'],
            'github_username'    => $input['github_username'],
            'github_created_at'  => Carbon::parse($input['github_created_at']),
            'resolver_github_id' => $input['resolver_github_id'] ?? null,
            'resolved_at'        => isset($input['resolved_at']) ? Carbon::parse($input['resolved_at']) : null,
            'state'              => $input['state'],
        ]);
    }
}
