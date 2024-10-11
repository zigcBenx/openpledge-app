<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateNewRepositoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
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
