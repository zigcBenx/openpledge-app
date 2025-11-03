<?php

namespace App\Actions\Repository;

use App\Models\Repository;
use App\Models\RepositorySettings;
use App\Models\Label;
use App\Actions\Github\LabelActions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

/**
 * Updates repository settings for a given repository.
 * Validates user permissions and input data.
 */
class UpdateRepositorySettings
{
    public static function update(int $repositoryId, array $data)
    {
        $user = Auth::user();
        $repository = Repository::findOrFail($repositoryId);

        // Check if user owns the repository
        if ($repository->user_id !== $user->id) {
            throw ValidationException::withMessages([
                'repository' => ['You do not have permission to modify this repository.']
            ]);
        }

        // Handle pledgeable label requirement
        if (isset($data['require_pledgeable_label']) && $data['require_pledgeable_label']) {
            // Set allowed_labels to only include 'Pledgeable'
            $data['allowed_labels'] = ['Pledgeable'];
            
            // Try to create the label on GitHub if repository has installation
            if ($repository->github_installation_id) {
                try {
                    $repoTitle = explode('/', $repository->title);
                    $owner = $repoTitle[0];
                    $repoName = $repoTitle[1];
                    
                    // Check if label exists first
                    if (!LabelActions::checkLabelExists($repository->github_installation_id, $owner, $repoName)) {
                        LabelActions::createPledgeableLabel($repository->github_installation_id, $owner, $repoName);
                    }
                } catch (\Exception $e) {
                    // Don't fail the entire operation if label creation fails
                    logger('[WARNING] Could not create Pledgeable label on GitHub: ' . $e->getMessage());
                }
            }
        } elseif (isset($data['require_pledgeable_label']) && !$data['require_pledgeable_label']) {
            // Clear allowed labels if disabling requirement
            $data['allowed_labels'] = null;
        }

        // Validate allowed_labels if provided (for future multi-label support)
        if (isset($data['allowed_labels']) && is_array($data['allowed_labels'])) {
            // For now, we only support 'Pledgeable' but keep validation flexible
            $validLabels = array_merge(Label::$allowedLabels, ['Pledgeable']);
            $invalidLabels = array_diff($data['allowed_labels'], $validLabels);
            if (!empty($invalidLabels)) {
                throw ValidationException::withMessages([
                    'allowed_labels' => ['Invalid labels: ' . implode(', ', $invalidLabels)]
                ]);
            }
        }

        // Validate donation amounts
        if (isset($data['min_donation_amount']) && isset($data['max_donation_amount'])) {
            if ($data['min_donation_amount'] > $data['max_donation_amount']) {
                throw ValidationException::withMessages([
                    'min_donation_amount' => ['Minimum donation amount cannot be greater than maximum donation amount.']
                ]);
            }
        }

        // Update or create repository settings
        $settings = $repository->settings()->updateOrCreate(
            ['repository_id' => $repositoryId],
            [
                'allowed_labels' => $data['allowed_labels'] ?? null,
                'enable_donation_expiry' => $data['enable_donation_expiry'] ?? false,
                'default_expiry_days' => $data['default_expiry_days'] ?? null,
                'min_donation_amount' => $data['min_donation_amount'] ?? null,
                'max_donation_amount' => $data['max_donation_amount'] ?? null,
            ]
        );

        return $settings->fresh();
    }
}