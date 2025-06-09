<?php

namespace App\Console\Commands;

use App\Jobs\GenerateInvoiceNumberJob;
use App\Models\Donation;
use Illuminate\Console\Command;

class DoSomething extends Command
{
    protected $signature = 'do:something';

    public function handle()
    {
        $donations = Donation::with('user')->get();

        foreach($donations as $donation) {
            $this->info('Processing donation: ' . $donation->id);
            dispatch(new GenerateInvoiceNumberJob([], $donation->id))->onQueue('default');
        }
    }
}
