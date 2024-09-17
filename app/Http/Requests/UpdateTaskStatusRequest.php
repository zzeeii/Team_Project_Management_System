<?php

namespace App\Http\Requests;

use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;

class UpdateTaskStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Only testers should be able to update the task status.
     *
     * @return bool
     */
    public function authorize()
    {
        $user = Auth::user();
        $projectId = $this->route('project');
        $project = Project::find($projectId);

        if (!$project) {
            return false; 
        }
        $role = $project->users()->where('user_id', $user->id)->first()?->pivot->role;
     
            return $role === 'developer';
      
}

    public function rules()
    {
            return [
                'status' => 'required|in:new,in_progress,completed,failed', 
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
            'status.required' => 'Task status is required.',
            'status.in' => 'Task status must be one of: new, in_progress, or done.',
        ];
    }
}
