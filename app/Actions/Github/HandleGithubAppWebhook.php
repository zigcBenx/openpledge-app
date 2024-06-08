<?php

namespace App\Actions\Github;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class handleGithubAppWebhook
{
    public static function run(Request $request)
    {
        $payload = $request->all();
        \Log::info('Webhook received: ', $payload);
        return response()->json(['status' => 'success'], 200);
    }
}
