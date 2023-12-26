<?php

namespace App\Http\Controllers;

use App\Actions\Donation\CreateNewDonation;
use App\Actions\Donation\GetDonationById;
use App\Actions\Donation\GetDonations;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DonationController extends Controller
{
    public function store(Request $request)
    {
        return CreateNewDonation::create($request->all());
    }

    public function index()
    {
        $donations = GetDonations::get();
        return Inertia::render('Donations/Index', [
            'donations' => $donations
        ]);
    }

    public function show($id)
    {
        $donation = GetDonationById::get($id);
        return Inertia::render('Donations/Show', [
            'donation' => $donation
        ]);
    }
}