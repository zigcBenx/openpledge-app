<?php

namespace App\Actions\Issue;

use App\Models\Issue;
use Carbon\Carbon;

class GetIssues
{
    public static function get()
    {
        return Issue::with('programmingLanguages')
            ->withSum('donations', 'net_amount')
            ->get();
    }

    public static function getWithActiveDonations($filters = null, $offset = 0, $limit = 10)
    {
        $today = Carbon::now()->toDateString();

        $query = Issue::query()
            ->with('programmingLanguages', 'repository.programmingLanguages', 'userFavorite', 'labels')
            ->withSum([
                'donations' => function ($query) use ($today) {
                    $query->where(function ($query) use ($today) {
                        $query->whereNull('expire_date')
                            ->orWhere('expire_date', '>', $today);
                    });
                }
            ], 'net_amount')
            ->having('donations_sum_net_amount', '>', 0)
            ->having('state', 'open');

        if ($filters) {
            if (isset($filters['range'])) {
                [$minRange, $maxRange] = explode('-', $filters['range']);
                $query->having('donations_sum_net_amount', '>=', (int) $minRange)
                    ->having('donations_sum_net_amount', '<=', (int) $maxRange);
            }

            if (isset($filters['date'])) {
                [$month, $year] = explode('-', $filters['date']);
                $query->whereMonth('created_at', $month)->whereYear('created_at', $year);
            }

            if (isset($filters['languages'])) {
                $languagesArray = explode(',', $filters['languages']);
                $query->whereHas('repository.programmingLanguages', function ($query) use ($languagesArray) {
                    $query->whereIn('name', $languagesArray);
                });
            }

            if (isset($filters['labels'])) {
                $labelsArray = explode(',', $filters['labels']);
                $query->whereHas('labels', function ($query) use ($labelsArray) {
                    $query->whereIn('name', $labelsArray);
                });
            }
        }

        $query = $query->orderByDesc('donations_sum_net_amount')
            ->skip($offset)
            ->take($limit);

        $issues = $query->get();

        $issues->each(function ($issue) {
            $issue->favorite = $issue->userFavorite->isNotEmpty();
        });

        return $issues;
    }
}
