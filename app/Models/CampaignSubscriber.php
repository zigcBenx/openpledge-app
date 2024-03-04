<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CampaignSubscriber extends Model
{
    use HasFactory;

    protected $table = 'campaign_subscriber';

    // protected $fillable = [];

    public function subscriber()
    {
        return $this->belongsTo(Subscriber::class);
    }
    
    public function campaign()
    {
        return $this->belongsTo(Subscriber::class);
    }
}