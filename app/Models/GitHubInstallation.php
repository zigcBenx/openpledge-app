<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GitHubInstallation extends Model
{
    use HasFactory;

    protected $table = 'github_installations';

    protected $fillable = [
        'user_id',
        'installation_id',
        'setup_action',
        'access_token',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
