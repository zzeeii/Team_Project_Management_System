<?php


namespace App\Http\Requests;

use App\Models\Project;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class StoreProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
         $user = Auth::user();
         
        return $user->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ];
    }

    /**
     * Customize the error messages for validation.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => 'The project name is required.',
            'name.string' => 'The project name must be a valid string.',
            'name.max' => 'The project name may not exceed 255 characters.',
        ];
    }
}

