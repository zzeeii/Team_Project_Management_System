<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class AddUserToProjectRequest extends FormRequest
{
    public function authorize(): bool
    { 
        $user = Auth::user();
         
        return $user->isAdmin();
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'role' => 'required|in:manager,developer,tester',
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'The user ID is required.',
            'user_id.exists' => 'The selected user does not exist.',
            'role.required' => 'The role is required.',
            'role.in' => 'The role must be one of: manager, developer, tester.',
        ];
    }
}
