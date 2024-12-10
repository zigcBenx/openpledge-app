<?php

namespace App\Actions\Issue;

use Illuminate\Support\Facades\Auth;

class SolveIssue
{
    public static function solve($issue_id)
    {
        $user = Auth::user();

        $exists = $user->active_issues()->where('issue_id', $issue_id)->exists();

        if ($exists) {
            $user->active_issues()->detach($issue_id);

            return response()->json(['message' => "Issue removed from Active issues."]);
        } else {
            $user->active_issues()->attach($issue_id);

            return response()->json(['message' => "Issue added to Active issues. View all >"]);
        }
    }
}