<?php

namespace App\Actions\Issue;

use App\Models\Issue;

class GetIssueById
{
    public static function get($id)
    {
        $issue = Issue::with([
            'repository' => function ($query) {
                $query->with('githubInstallation');
            },
            'donations.user',
            'userFavorite'
        ])->find($id)->append('donation_sum');

        $issue->favorite = $issue->userFavorite->isNotEmpty();
        $issue->isAuthUsersActiveIssue = $issue->isAuthUsersActiveIssue();

        return $issue;
    }
}
