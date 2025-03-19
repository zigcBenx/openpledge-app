<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WalletTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'amount',
        'is_withdrawn',
        'contributor_id',
        'donation_id',
    ];

    public function donation(): BelongsTo
    {
        return $this->belongsTo(Donation::class);
    }

    public function contributor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'contributor_id');
    }
}
