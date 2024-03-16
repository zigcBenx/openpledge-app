<?php

namespace App\Console\Commands;

use App\Models\Campaign;
use App\Models\CampaignSubscriber;
use App\Models\Subscriber;
use Carbon\Carbon;
use Illuminate\Console\Command;

class RunCampaignsCommand extends Command
{
    protected $signature = 'campaigns:run';

    public function handle()
    {
        $campaigns = $this->getCampaigns();

        foreach ($campaigns as $campaign) {
            $subscribersReceived = CampaignSubscriber::where('campaign_id', $campaign->id)->pluck('subscriber_id');
            $subscribersToSend = Subscriber::whereNotIn('id', $subscribersReceived)->get();

            // Handle recurring campaigns for new users
            if (Carbon::today()->isAfter($campaign->start_time)) {
                if ($campaign->is_recurring_for_new_users) {
                    $newSubscribers = Subscriber::where('created_at', '>=', now()->subDays($campaign->new_user_delay_days))->get();
                    $this->sendCampaign($campaign, $$newSubscribers);
                }
                continue;
            }
            $this->sendCampaign($campaign, $subscribersToSend);
        }

        $this->info('Campaigns sent successfully.');
    }

    private function sendCampaign($campaign, $subscribers)
    {
        $this->info('Campaign: ' . $campaign->name . ' is sent to: ' . join(',',array_map(function($s){return $s['email'];},$subscribers->toArray())));
    }

    private function getCampaigns()
    {
        return Campaign::where('is_enabled', true)
            ->whereDate('start_time', '<=', Carbon::today())
            ->get();
    }
}
