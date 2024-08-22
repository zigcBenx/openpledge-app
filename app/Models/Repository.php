<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Repository extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'github_url',
        'github_id',
        'user_avatar',
        'user_id',
        'github_installation_id'
    ];

    public function issues()
    {
        return $this->hasMany(Issue::class);
    }

    public function donations()
    {
        return $this->morphMany(Donation::class, 'donatable');
    }

    public function getDonationSumAttribute()
    {
        return $this->donations()->sum('amount');
    }

    public function getIssuesDonationSumAttribute()
    {
        return $this->issues()->donations()->sum('amount');
    }

    public function programmingLanguages()
    {
        return $this->morphToMany(ProgrammingLanguage::class, 'programming_languageable');
    }

    public function githubInstallation()
    {
        return $this->belongsTo(GitHubInstallation::class, 'github_installation_id', 'installation_id');
    }

    public function userFavorite()
    {
        return $this->morphMany(Favorite::class, 'favorable')
            ->where('user_id', Auth::id());
    }
}
