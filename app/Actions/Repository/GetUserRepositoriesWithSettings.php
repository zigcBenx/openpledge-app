<?php

namespace App\Actions\Repository;

use App\Models\Repository;
use Illuminate\Support\Facades\Auth;

/**
 * Retrieves the repositories associated with an authenticated user Id.
 * This action fetches repositories that were installed via the GitHub App.
 * Additionally, it loads the related issues, calculates the total donation amount for each issue,
 * and includes repository settings.
 */
class GetUserRepositoriesWithSettings
{
    public static function get()
    {
        $user = Auth::user();
        
        return Repository::with([
            'issues' => function ($query) {
                $query->withSum('donations', 'net_amount');
            },
            'settings'
        ])
        ->withSum('donations', 'net_amount')
        ->where('user_id', $user->id)
        ->get()
        ->map(function ($repository) {
            // Calculate total donations from issues
            $issuesDonationsSum = $repository->issues->sum('donations_sum_net_amount') ?? 0;
            
            // Add repository donations (if any)
            $repositoryDonationsSum = $repository->donations_sum_net_amount ?? 0;
            
            // Total donations for this repository
            $totalDonations = $issuesDonationsSum + $repositoryDonationsSum;
            
            return [
                'id' => $repository->id,
                'title' => $repository->title,
                'github_url' => $repository->github_url,
                'total_donations' => $totalDonations,
                'issues_count' => $repository->issues->count(),
                'settings' => $repository->settings ? [
                    'allowed_labels' => $repository->settings->allowed_labels,
                    'enable_donation_expiry' => $repository->settings->enable_donation_expiry,
                    'default_expiry_days' => $repository->settings->default_expiry_days,
                    'min_donation_amount' => $repository->settings->min_donation_amount,
                    'max_donation_amount' => $repository->settings->max_donation_amount,
                ] : null,
            ];
        });
    }
}