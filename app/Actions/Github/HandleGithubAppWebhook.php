<?php

namespace App\Actions\Github;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Issue;
use App\Models\Donation;

class handleGithubAppWebhook
{
    public static function run(Request $request)
    {
        $payload = $request->all();

        Log::info('Webhook received: ', $payload);

        DB::beginTransaction();

        try {
            $action = $payload['action'];
            $issueUrl = $payload['issue']['html_url'];

            $issue = Issue::where('github_url', $issueUrl)->first();

            if ($issue) {
                $issue->state = $action;
                $issue->save();

                // TODO: Implement the logic to pay out the person who resolved the issue
                //Donation::where('donatable_id', $issue->id)->update(['paid' => true]);

                Log::info("Issue state updated to $action: ", ['issue_id' => $issue->id]);

            } else {
                Log::warning('No issue found for URL: ', ['url' => $issueUrl]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Transaction failed: ', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Transaction failed'], 500);
        }

        return response()->json(['status' => 'success'], 200);
    }
}
