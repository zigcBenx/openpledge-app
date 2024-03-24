<?php

namespace App\Console\Commands;

use App\Actions\Campaign\SendCampaign;
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
            $subscribersReceived = CampaignSubscriber::where('campaign_id', $campaign->id)->get();
            $subscribersToSend = Subscriber::whereNotIn('id', $subscribersReceived->pluck('subscriber_id'))->get();

            // Handle recurring campaigns for new users
            if (Carbon::today()->isAfter($campaign->start_time)) {
                if ($campaign->is_recurring_for_new_users) {
                    $newSubscribers = Subscriber::whereDate('created_at','>=', $campaign->start_time)
                                                    ->whereDate('created_at', now()->subDays($campaign->new_user_delay_days))->get();

                    $newSubscribers = $newSubscribers->filter(function($subscriber) use ($subscribersReceived) {
                        return !in_array($subscriber->id, $subscribersReceived->pluck('subscriber_id')->toArray());
                    });

                    $this->sendCampaign($campaign, $newSubscribers);
                }
                continue;
            }
            $this->sendCampaign($campaign, $subscribersToSend);
        }

        $this->info('Campaigns sent successfully.');
    }

    private function sendCampaign($campaign, $subscribers)
    {
        if (!$subscribers->count()) {
            return;
        }

        // Insert into CampaignSubscriber model
        foreach ($subscribers as $subscriber) {
            CampaignSubscriber::create([
                'campaign_id' => $campaign->id,
                'subscriber_id' => $subscriber->id,
            ]);
            SendCampaign::send($subscriber->email, $campaign->content);
        }
    }

    private function getCampaigns()
    {
        return Campaign::where('is_enabled', true)
            ->whereDate('start_time', '<=', Carbon::today())
            ->get();
    }
}
