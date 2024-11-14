<?php

namespace App\Http\Controllers;

use App\Actions\Donation\CreateNewDonation;
use App\Actions\Donation\GetDonationById;
use App\Actions\Donation\GetDonations;
use App\Http\Requests\CreateDonationRequest;
use Inertia\Inertia;

class DonationController extends Controller
{
    public function store(CreateDonationRequest $request)
    {
        return CreateNewDonation::create($request->validated());
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