<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    use HasFactory;

    protected $fillable = [
        'donatable_id',
        'donatable_type',
        'amount',
        'transaction_id',
        'donor_id'
    ];
}
