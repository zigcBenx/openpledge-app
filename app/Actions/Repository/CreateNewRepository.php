<?php

namespace App\Actions\Repository;

use App\Models\Repository;
use Illuminate\Support\Facades\Validator;

class CreateNewRepository
{
    public static function create(array $input): Repository
    {      
        Validator::make($input, [
            'title'       => ['required', 'string', 'max:255'],
            'github_url'  => ['required', 'string', 'url',],
            'github_id'   => ['required'],
            'user_avatar' => ['string'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'github_installation_id' => ['string'],
        ])->validate();

        return Repository::updateOrCreate([
            'github_id'   => $input['github_id'],
        ],[
            'title'       => $input['title'],
            'github_url'  => $input['github_url'],
            'user_avatar' => $input['user_avatar'],
            'user_id' => $input['user_id'],
            'github_installation_id' => $input['github_installation_id'] ?? null
        ]);
    }
}
