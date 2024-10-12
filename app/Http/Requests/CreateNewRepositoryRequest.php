<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateNewRepositoryRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title'                  => ['required', 'string', 'max:255'],
            'github_url'             => ['required', 'string', 'url',],
            'github_id'              => ['required'],
            'user_avatar'            => ['string'],
            'user_id'                => ['required', 'integer', 'exists:users,id'],
            'github_installation_id' => ['string']
        ];
    }
}
