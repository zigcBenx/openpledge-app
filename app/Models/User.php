<?php

namespace App\Models;

use App\Casts\MoneyCast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Cashier\Billable;
use Carbon\Carbon;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use HasRoles;
    use Billable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'github_id',
        'profile_photo_path',
        'auth_type',
        'stripe_id',
        'is_pledger',
        'is_contributor',
        'job_title',
        'company_id',
        'is_pledging_anonymously'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    public function programmingLanguages()
    {
        return $this->morphToMany(ProgrammingLanguage::class, 'programming_languageable');
    }

    public function githubInstallations()
    {
        return $this->hasOne(GitHubInstallation::class);
    }

    public function hasGitHubAppInstalled()
    {
        return $this->githubInstallations()->exists();
    }

    public function active_issues()
    {
        return $this->belongsToMany(Issue::class, 'user_solve_issue');
    }

    public function isContributor()
    {
        return (bool) $this->is_contributor;
    }

    public function isResolver()
    {
        return (bool) $this->is_pledger;
    }

    public function getGitHubAccessToken()
    {
        $installation = $this->githubInstallations()->first();

        if ($installation && !empty($installation->access_token)) {
            return $installation->access_token;
        }

        return null;
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function walletTransactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class, 'contributor_id');
    }

    public function pendingWalletTransactions(): HasMany
    {
        return $this->walletTransactions()->where('is_withdrawn', false);
    }

    public function resolvedIssues(): HasMany
    {
        return $this->hasMany(Issue::class, 'resolver_github_id', 'github_id');
    }

    public function getWalletAmountAttribute(): float
    {
        $amount = $this->walletTransactions()->sum('amount');
        return app(MoneyCast::class)->get($this, 'wallet_sum', $amount, []);
    }

    public function getWalletAmountAvailableAttribute(): float
    {
        $amount = $this->walletTransactions()
            ->where('is_withdrawn', false)
            ->sum('amount');
        return app(MoneyCast::class)->get($this, 'wallet_available_sum', $amount, []);
    }

    public function isEligibleForPayout(): bool
    {
        return $this->stripe_id !== null;
    }

    public function latestPayoutTransaction()
    {
        return $this->walletTransactions()
            ->where('is_withdrawn', true)
            ->orderBy('withdrawn_at', 'desc')
            ->first();
    }

    public function hasPayoutThisMonth(): bool
    {
        $latestPayout = $this->latestPayoutTransaction();

        return $latestPayout &&
               Carbon::parse($latestPayout->withdrawn_at)->isCurrentMonth();
    }
}
