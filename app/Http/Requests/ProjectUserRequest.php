<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Project;
use App\Models\Role;

class ProjectUserRequest extends FormRequest
{
    public function authorize()
    {
   
        $projectId = $this->route('project');
        $project = Project::findOrFail($projectId);
        $user = Auth::user();
        $allowedRoles = ['manager', 'developer', 'tester'];

        return $project->users()
                       ->where('user_id', $user->id)
                       ->whereIn('role', $allowedRoles)
                       ->exists();
    }

    public function rules()
    {
        return [
         
        ];
    }
}
