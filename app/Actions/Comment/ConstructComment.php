<?php

namespace App\Actions\Comment;

class ConstructComment
{
    public static function constructPledgeComment($amount, $donorName, $issueId)
    {
        $appUrl = env('APP_URL');
        $issueLink = "{$appUrl}/issues/{$issueId}";

        $comment = view('comments.new_pledge', [
            'amount' => $amount,
            'donorName' => $donorName,
            'issueLink' => $issueLink
        ])->render();

        return $comment;
    }
}
