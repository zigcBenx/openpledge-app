<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Stripe\Exception\OAuth\OAuthErrorException;
use Stripe\OAuth;
use Symfony\Component\HttpFoundation\Response;

class StripeConnectController extends Controller
{
    public function redirectToStripe(Request $request): Response
    {
        return Inertia::location(config('app.stripe_connect_url'));
    }

    public function handleStripeConnectCallback(Request $request): RedirectResponse
    {
        try {
            $user = Auth::user();
            $response = OAuth::token([
                'grant_type' => 'authorization_code',
                'code' => $request->code
            ]);

            User::query()->where('id', $user->id)->update(['stripe_id' => $response->stripe_user_id]);
            return redirect()->route('home')->with('success', 'Successfully connected to stripe');
        } catch (OAuthErrorException $e) {
            return redirect()->route('home')->with('error', $e->getMessage());
        }
    }
}