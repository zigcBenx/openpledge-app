<?php

namespace App\Models;

use App\Casts\MoneyCast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PendingDonation extends Model
{
    use HasFactory;

    protected $fillable = [
        'amount',
        'is_claimed',
        'donation_id',
        'claimed_at',
        'user_github_name',
    ];

    protected $casts = [
        'amount' => MoneyCast::class
    ];

    public function donation(): BelongsTo
    {
        return $this->belongsTo(Donation::class);
    }
}
