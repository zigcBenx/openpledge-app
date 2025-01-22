<?php

namespace App\Actions\Issue;

use App\Models\Issue;
use Carbon\Carbon;

class CreateNewIssue
{
    public static function create(array $input): Issue
    {            
        return Issue::create([
            'title'              => $input['title'],
            'description'        => $input['description'],
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
