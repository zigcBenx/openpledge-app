<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['name', 'address', 'vat_id', 'city', 'postal_code', 'state', 'country'];

    protected $casts = [
        'country' => 'array',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
