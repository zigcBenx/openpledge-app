<?php

namespace App\Models;

use App\Casts\MoneyCast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RepositorySettings extends Model
{
    use HasFactory;

    protected $fillable = [
        'repository_id',
        'allowed_labels',
        'enable_donation_expiry',
        'default_expiry_days',
        'min_donation_amount',
        'max_donation_amount',
    ];

    protected $casts = [
        'allowed_labels' => 'array',
        'enable_donation_expiry' => 'boolean',
        'min_donation_amount' => MoneyCast::class,
        'max_donation_amount' => MoneyCast::class,
    ];

    public function repository()
    {
        return $this->belongsTo(Repository::class);
    }
}
