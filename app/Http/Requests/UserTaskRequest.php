<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UserTaskRequest extends FormRequest
{
    public function authorize()
    {
      return  Auth::user();
    }

    public function rules()
    {
        return [
            'project_id' => 'required|exists:projects,id',
        ];
    }
    protected function prepareForValidation()
    {
       
        $this->merge([
            'project_id' => $this->route('project'), 
        ]);
    }

    public function messages()
    {
        return [
            'project_id.required' => 'The project ID is required.',
            'project_id.exists' => 'The specified project does not exist.',
        ];
    }
}
