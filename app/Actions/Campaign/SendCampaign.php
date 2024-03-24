<?php

namespace App\Actions\Campaign;

use App\Mail\CampaignMail;
use Exception;
use Illuminate\Support\Facades\Mail;

class SendCampaign
{
    public static function send($emails, $content)
    {
        try {
            Mail::to($emails)->send(new CampaignMail($content));
            return response()->json(['message' => 'Campaign sent successfully']);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}
