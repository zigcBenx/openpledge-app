<?php

namespace App\Actions\Repository;

use App\Models\Repository;

class GetRepositoryById
{
    public static function get($id)
    {
        return Repository::with('issues')->find($id);
    }
}
