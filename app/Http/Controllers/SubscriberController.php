<?php

namespace App\Http\Controllers;

use App\Mail\ThankYouMail;
use App\Models\Subscriber;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class SubscriberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
        if (!request()->user()->hasRole('admin')) {
            return redirect()->route('home');
        }
        
        $subscribers = Subscriber::orderByDesc('created_at')->get();

        $counters = [
            [
                'name' => 'Today\'s subscribers',
                'count' => $subscribers->where('created_at', '>=', Carbon::today())->count(),
            ],
            [
                'name' => 'All subscribers',
                'count' => $subscribers->count()
            ],
            [
                'name' => 'All sponsors',
                'count' => $subscribers->where('campaign', 'sponsor')->count(),
            ],
            [
                'name' => 'Hackathon interested',
                'count' => $subscribers->where('campaign', 'hackathon')->count(),
            ],
        ];

        return Inertia::render('Subscribers', [
            'subscribers' => $subscribers,
            'counters'    => $counters
        ]);
    }

    public function subscribeUser(Request $request)
    {
        try {
            $data = $request->validate([
                'email'    => 'required|email',
                'source'   => '',
                'campaign' => '',
            ]);

            $email = $data['email'];
            $source = $data['source'];
            $campaign = $data['campaign'];

            Subscriber::create([
                'email'             => $email,
                'source'            => $source,
                'campaign'          => $campaign,
                'unsubscribe_token' => hash('sha256',$email)
            ]);

            // Send email
            Mail::to($email)->send(new ThankYouMail($email));

            return response()->json(['message' => 'Email sent successfully']);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->validator->errors()], 422);
        }
    }

    public function unsubscribe(Request $request)
    {
        $token = $request->query('token');

        $subscriber = Subscriber::where('unsubscribe_token', $token)->first();
        
        if (!$subscriber) {
            return response()->json(['message' => 'Subscriber not found. Please contact us on: info@openpledge.io']);
        }
        
        $subscriber->update(['newsletter' => false]);
        return response()->json(['message' => 'You have been successfully unsubscribed from our newsletter.']);
    }
}
