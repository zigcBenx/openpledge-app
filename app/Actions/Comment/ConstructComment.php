<?php

namespace App\Actions\Comment;

class ConstructComment
{
    public static function constructPledgeComment($amount, $donorName, $issueId)
    {
        $appUrl = config('app.url');
        $issueLink = "{$appUrl}/issues/{$issueId}";

        $comment = view('comments.new_pledge', [
            'amount' => $amount,
            'donorName' => $donorName,
            'issueLink' => $issueLink
        ])->render();

        return $comment;
    }
}
