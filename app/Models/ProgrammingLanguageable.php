<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgrammingLanguageable extends Model
{
    use HasFactory;

    protected $table = 'programming_languageables';

    protected $fillable = [
        'programming_language_id',
        'programming_languageable_id',
        'programming_languageable_type',
    ];
}
