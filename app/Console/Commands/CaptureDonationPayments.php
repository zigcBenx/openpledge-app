<?php

namespace App\Console\Commands;

use App\Models\Donation;
use Carbon\Carbon;
use Illuminate\Console\Command;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\ApiErrorException;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class CaptureDonationPayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:capture-payments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $today = Carbon::now()->toDateString();
            $donations = Donation::query()
                ->with('donatable')
                ->where(function ($query) use ($today) {
                    $query->where('expire_date', '>', $today)
                        ->orWhereNull('expire_date');
                })
                ->where('paid', false)
                ->get();

            $client = new Client([
                'base_uri' => 'https://api.github.com/',
                'headers' => [
                    'Accept' => 'application/vnd.github.v3+json',
                ],
            ]);
            
            foreach ($donations as $donation) {
                $repository = preg_replace('#https://github\.com/#', '', $donation->donatable->github_url);
                $repository = preg_replace('/\/pull\//', '/pulls/', $repository);
                $response = $client->request('GET', 'repos/'.$repository);
                $data = json_decode($response->getBody(), true);
                if(!empty($data['merged'])) {
                    if($this->capture($donation->transaction_id)) {
                        Donation::query()->where('id', $donation->id)->update(['paid' => true]);
                        
                        /*
                        TODO
                        Here we will need to know to which connected acc need to send money
                        Need to add github acc id to users and check if user has that id, if not notify the user send email or something to connect on stripe
                        Also this need to be tested live
                        $transfer = \Stripe\Transfer::create([
                            "amount" => 1000,
                            "currency" => "usd",
                            "destination" => "{{CONNECTED_STRIPE_ACCOUNT_ID}}",
                        ]);
                        
                        */

                    }
                    $this->info("Pull request #$donation->id has been merged.");
                } else {
                    $this->info("Pull request #$donation->id has not been merged.");
                }
            }
        } catch(\Exception $e) {
            Log::alert($e->getMessage());
        }
    }

    private function capture($paymentId)
    {
        try {
            Stripe::setApiKey(config('app.stripe_secret'));

            $paymentIntent = PaymentIntent::retrieve($paymentId);
            $paymentIntent->capture();

            if ($paymentIntent->status === 'succeeded') {
                return true;
            } else {
                return false;
            }
        } catch (ApiErrorException $e) {
            Log::alert($e->getMessage());
            return false;
        }
    }
}
