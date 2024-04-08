<?php

namespace App\Console\Commands;

use App\Models\Subscriber;
use Illuminate\Console\Command;

class AddUnsubscribeTokensCommand extends Command
{
    protected $signature = 'users:addUnsubscribeTokens';

    public function handle()
    {
        $subscribers = Subscriber::all();
        foreach($subscribers as $subscriber) {
            $subscriber->update(['unsubscribe_token' => hash('sha256',$subscriber->email)]);
        }
    }
}
