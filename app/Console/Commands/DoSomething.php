<?php

namespace App\Console\Commands;

use App\Jobs\GenerateInvoiceNumberJob;
use App\Models\Donation;
use App\Models\Invoice;
use Illuminate\Console\Command;

class DoSomething extends Command
{
    protected $signature = 'do:something';
    protected $description = 'Generate invoices for donations that do not have them yet';

    public function handle()
    {
        $donations = Donation::with('user')->get();
            
        $processedCount = 0;
        $skippedCount = 0;
        $errorCount = 0;

        $this->info("Found {$donations->count()} donations to check");

        foreach($donations as $donation) {
            try {
                // Check if invoice already exists for this donation
                $existingInvoice = Invoice::where('donation_id', $donation->id)->first();
                
                if ($existingInvoice) {
                    $this->line("✅ Skipping donation {$donation->id} - invoice exists: {$existingInvoice->number}");
                    $skippedCount++;
                    continue;
                }
                
                // Validate donation has required data
                if (!$donation->user) {
                    $this->error("❌ Donation {$donation->id} has no user - skipping");
                    $errorCount++;
                    continue;
                }
                
                if (!$donation->gross_amount) {
                    $this->error("❌ Donation {$donation->id} has no amount - skipping");
                    $errorCount++;
                    continue;
                }
                
                $this->info("🔄 Processing donation: {$donation->id} (User: {$donation->user->name}, Amount: {$donation->gross_amount})");
                
                dispatch(new GenerateInvoiceNumberJob([], $donation->id))->onQueue('default');
                $processedCount++;
                
            } catch (\Exception $e) {
                $this->error("❌ Error processing donation {$donation->id}: " . $e->getMessage());
                $errorCount++;
            }
        }
        
        $this->info("\n📊 Summary:");
        $this->info("✅ Processed: {$processedCount} donations");
        $this->info("⏭️  Skipped: {$skippedCount} (already have invoices)");
        $this->info("❌ Errors: {$errorCount}");
        
        if ($processedCount > 0) {
            $this->info("\n🚀 Jobs dispatched to queue. Monitor with: php artisan queue:work");
        }
    }
}
