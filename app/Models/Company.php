<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['name', 'address', 'vat_id', 'should_bill_company'];

    protected $casts = [
        'should_bill_company' => 'boolean',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
