<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Issue extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'github_url',
        'github_id',
        'repository_id',
        'user_avatar'
    ];

    public function repository()
    {
        return $this->belongsTo(Repository::class);
    }

    public function donations()
    {
        return $this->morphMany(Donation::class, 'donatable');
    }
}
