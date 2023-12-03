<?php

namespace App\Actions\Repository;

use App\Models\Repository;

class GetRepositories
{
    public static function get()
    {
        return Repository::all();
    }
}
