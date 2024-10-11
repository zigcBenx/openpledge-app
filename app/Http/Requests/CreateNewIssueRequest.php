<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateNewIssueRequest extends FormRequest
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
            'title'              => ['required', 'string', 'max:255'],
            'github_url'         => ['required', 'string', 'url'],
            'github_id'          => ['required', 'unique:issues,github_id'],
            'repository_id'      => ['required', 'exists:repositories,id'],
            'user_avatar'        => ['nullable', 'string'],
            'github_username'    => ['nullable', 'string'],
            'github_created_at'  => ['required', 'date'],
            'resolver_github_id' => ['nullable', 'string'],
            'resolved_at'        => ['nullable', 'date'],
            'state'              => ['required', 'string', 'max:255']
        ];
    }
}
