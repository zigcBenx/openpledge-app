<?php

namespace App\Models;

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
        'resolved_at'
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

    public function getDonationSumAttribute()
    {
        return $this->donations()->sum('amount');
    }

    public function userFavorite()
    {
        return $this->morphMany(Favorite::class, 'favorable')
            ->where('user_id', Auth::id());
    }
}
