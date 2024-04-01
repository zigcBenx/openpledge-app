<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'is_enabled', 'is_recurring_for_new_users',
        'new_user_delay_days', 'start_time', 'content'
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'is_recurring_for_new_users' => 'boolean',
        'start_time' => 'datetime',
    ];

    public function subscribers()
    {
        return $this->belongsToMany(Subscriber::class)->withTimestamps();
    }
}