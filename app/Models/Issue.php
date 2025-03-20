<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Issue extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'github_url',
        'github_id',
        'repository_id',
        'user_avatar',
        'state',
        'labels',
        'github_username',
        'github_created_at',
        'resolver_github_id',
        'resolved_at',
        'description'
    ];

    public function repository()
    {
        return $this->belongsTo(Repository::class);
    }

    public function donations()
    {
        return $this->morphMany(Donation::class, 'donatable');
    }

    public function programmingLanguages()
    {
        return $this->morphToMany(ProgrammingLanguage::class, 'programming_languageable');
    }

    public function labels()
    {
        return $this->hasMany(Label::class);
    }

    public function getDonationSumAttribute()
    {
        return $this->donations()->sum('amount');
    }

    public function userFavorite()
    {
        return $this->morphMany(Favorite::class, 'favorable')
            ->where('user_id', Auth::id());
    }

    public function isAuthUsersActiveIssue()
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        return $user->active_issues()->where('issue_id', $this->id)->exists();
    }

    public function resolvers()
    {
        return $this->belongsToMany(User::class, 'user_solve_issue');
    }

    public function resolvedBy()
    {
        return $this->belongsTo(User::class, 'resolver_github_id', 'github_id');
    }

    public function getUnpaidDonations()
    {
        return $this->donations()
            ->where(function ($query) {
                $query->whereNull('expire_date')
                    ->orWhere('expire_date', '>', Carbon::now());
            })
            ->where('paid', false)
            ->get();
    }
}
