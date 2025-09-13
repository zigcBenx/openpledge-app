<?php

namespace App\Services;

use App\Models\Issue;
use Illuminate\Validation\ValidationException;

class DonationValidationService
{
    public static function canReceiveDonations(Issue $issue): bool
    {
        $repository = $issue->repository;
        
        // If no repository settings, allow donations
        if (!$repository->settings) {
            return true;
        }
        
        // If no label restrictions, allow donations
        if (!$repository->settings->allowed_labels || empty($repository->settings->allowed_labels)) {
            return true;
        }
        
        // Check if issue has the required "Pledgeable" label
        $issueLabels = $issue->labels()->pluck('name')->toArray();
        $requiredLabels = $repository->settings->allowed_labels;
        
        // Check if issue has at least one required label
        return !empty(array_intersect($issueLabels, $requiredLabels));
    }
    
    public static function getValidationMessage(Issue $issue): ?string
    {
        if (self::canReceiveDonations($issue)) {
            return null;
        }
        
        $repository = $issue->repository;
        $requiredLabels = $repository->settings->allowed_labels ?? [];
        
        if (in_array('Pledgeable', $requiredLabels)) {
            return 'This issue requires the "Pledgeable" label to receive donations. Please ask the repository maintainer to add this label.';
        }
        
        if (!empty($requiredLabels)) {
            $labelsList = implode(', ', $requiredLabels);
            return "This issue requires one of these labels to receive donations: {$labelsList}";
        }
        
        return 'This issue cannot receive donations due to repository restrictions.';
    }
    
    public static function validateDonationAmount(Issue $issue, float $amount): array
    {
        $repository = $issue->repository;
        $errors = [];
        
        if (!$repository->settings) {
            return $errors;
        }
        
        $settings = $repository->settings;
        
        if ($settings->min_donation_amount && $amount < ($settings->min_donation_amount / 100)) {
            $minAmount = number_format($settings->min_donation_amount / 100, 2);
            $errors[] = "Minimum donation amount for this repository is \${$minAmount}";
        }
        
        if ($settings->max_donation_amount && $amount > ($settings->max_donation_amount / 100)) {
            $maxAmount = number_format($settings->max_donation_amount / 100, 2);
            $errors[] = "Maximum donation amount for this repository is \${$maxAmount}";
        }
        
        return $errors;
    }
    
    public static function validateBeforeCreation(array $input): void
    {
        if ($input['donatable_type'] !== Issue::class) {
            return;
        }
        
        $issue = Issue::find($input['donatable_id']);
        if (!$issue) {
            throw ValidationException::withMessages(['issue' => ['Issue not found.']]);
        }
        
        if (!self::canReceiveDonations($issue)) {
            throw ValidationException::withMessages([
                'issue' => [self::getValidationMessage($issue)]
            ]);
        }
        
        $amountErrors = self::validateDonationAmount($issue, $input['gross_amount'] / 100);
        if (!empty($amountErrors)) {
            throw ValidationException::withMessages(['amount' => $amountErrors]);
        }
    }
}