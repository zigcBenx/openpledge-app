<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Cashier\Billable;

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

    public function getWalletAmountAttribute(): float
    {
        return $this->walletTransactions()->sum('amount');
    }
}
