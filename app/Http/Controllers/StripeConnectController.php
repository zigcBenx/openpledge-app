<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use App\Actions\Payment\StripeConnect;

class StripeConnectController extends Controller
{
    public function stripeConnect()
    {
        return Inertia::render('ConnectStripe');
    }

    public function createAccountLink(Request $request)
    {
        return StripeConnect::createAccountLink($request);
    }

    public function onboardingRefresh(Request $request)
    {
        return StripeConnect::handleOnboardingRefresh($request);
    }

    public function onboardingReturn(Request $request)
    {
        return StripeConnect::handleOnboardingReturn($request);
    }

    public function getDashboardLink()
    {
        return StripeConnect::getDashboardLink();
    }
}