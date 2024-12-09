<?php

namespace App\Actions\Comment;

class ConstructComment
{
    private const SHORT_PLEDGE_INTROS = [
        "🎯 Another pledge joins the bounty hunt! 🎯",
        "💫 The plot thickens! Another pledge has landed! 💫",
        "🚀 Look who's adding fuel to the rocket! 🚀",
        "🎮 Another player has entered the game with a new pledge! 🎮",
        "🌟 The bounty pool is growing stronger! 🌟",
        "🎪 The show gets better! New pledge alert! 🎪",
        "🎯 Bug bounty intensifies! 🎯",
        "🔥 Things are heating up with a fresh pledge! 🔥",
        "🎲 The stakes just got higher! 🎲",
        "🎁 Another treasure added to the chest! 🎁",
        "🌈 Double rainbow! Another pledge appears! 🌈",
        "⚡ Lightning strikes twice - new pledge incoming! ⚡",
        "🎨 Adding more color to this bounty! 🎨",
        "🎭 Plot twist: Another pledge appears! 🎭",
        "🎪 The circus is growing - new pledge alert! 🎪"
    ];

    public static function constructPledgeComment($amount, $donorName, $issueId, $expireDate = null)
    {
        $appUrl = config('app.url');
        $issueLink = "{$appUrl}/issues/{$issueId}";

        $comment = view('comments.new_pledge', [
            'amount' => $amount,
            'donorName' => $donorName,
            'issueLink' => $issueLink,
            'expireDate' => $expireDate
        ])->render();

        return $comment;
    }

    public static function constructShortPledgeComment($amount, $donorName, $issueId, $totalBounty, $expireDate = null)
    {
        $appUrl = config('app.url');
        $issueLink = "{$appUrl}/issues/{$issueId}";
        $intro = self::SHORT_PLEDGE_INTROS[array_rand(self::SHORT_PLEDGE_INTROS)];

        $comment = view('comments.short_pledge', [
            'intro' => $intro,
            'amount' => $amount,
            'donorName' => $donorName,
            'issueLink' => $issueLink,
            'expireDate' => $expireDate,
            'totalBounty' => $totalBounty
        ])->render();

        return $comment;
    }
}
