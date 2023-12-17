<?php

namespace App\Actions\Repository;

use App\Models\Repository;
use Illuminate\Support\Facades\Validator;

class CreateNewRepository
{
    public static function create(array $input): Repository
    {
        // TODO get value from request -> validate it
        // check if repository exists on github
        // if exists: check if it already exists in our database
            // if so: response with already existing message and link to that repository on open pledge
            // if not: create new repository and open it on open pledge
                    
        Validator::make($input, [
            'title'       => ['required', 'string', 'max:255'],
            'github_url'  => ['required', 'string', 'url',],
            'github_id'   => ['required','unique:repositories,github_id'],
            'user_avatar' => ['string'],
        ])->validate();

        return Repository::create([
            'title'       => $input['title'],
            'github_url'  => $input['github_url'],
            'github_id'   => $input['github_id'],
            'user_avatar' => $input['user_avatar'],
        ]);
    }
}
