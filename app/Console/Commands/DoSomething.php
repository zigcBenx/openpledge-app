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
        $this->info("Do Something");
    }
}
