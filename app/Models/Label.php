<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Exception;

class Label extends Model
{
    use HasFactory;

    protected $fillable = [
        'issue_id',
        'name',
    ];

    public static $allowedLabels = [
        'test',
        'feature',
        'bug',
        'enhancement',
        'documentation',
        'question',
        'invalid',
        'duplicate',
        'security',
        'pledgeable',
        'Pledgeable',
    ];

    // Validate labels before creating or updating, we only want to allow the labels that are in the allowedLabels array
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($label) {
            if (!in_array($label->name, self::$allowedLabels)) {
                throw new Exception("Label '{$label->name}' is not allowed.");
            }
        });

        static::updating(function ($label) {
            if (!in_array($label->name, self::$allowedLabels)) {
                throw new Exception("Label '{$label->name}' is not allowed.");
            }
        });
    }

    public function issue()
    {
        return $this->belongsTo(Issue::class);
    }
}
