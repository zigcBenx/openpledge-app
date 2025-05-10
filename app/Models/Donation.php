<?php

namespace App\Models;

use App\Casts\MoneyCast;
use App\Events\DonationCreatedEvent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Donation extends Model
{
    use HasFactory;

    protected $fillable = [
        'donatable_id',
        'donatable_type',
        'gross_amount',
        'net_amount',
        'fee_percentage',
        'transaction_id',
        'donor_id',
        'expire_date',
        'refund_transaction_id',
        'payout_transaction_id',
        'charge_id'
    ];

    protected $casts = [
        'gross_amount' => MoneyCast::class,
        'net_amount' => MoneyCast::class,
        'total_donated' => MoneyCast::class,
    ];

    protected $dispatchesEvents = [
        'created'  => DonationCreatedEvent::class
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'donor_id');
    }

    public function donatable(): MorphTo
    {
        return $this->morphTo();
    }

    public function walletTransactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class);
    }

    public function pendingDonations(): HasMany
    {
        return $this->hasMany(PendingDonation::class);
    }
}
