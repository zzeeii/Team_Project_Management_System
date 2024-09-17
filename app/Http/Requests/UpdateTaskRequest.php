<?php

namespace App\Http\Requests;

use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateTaskRequest extends FormRequest
{
    public function authorize()
    { 
        $user = Auth::user();
        $projectId = $this->route('project');
        $project = Project::findOrFail($projectId);

        $role = $project->users()->where('user_id', $user->id)->first()?->pivot->role;
        return $role === 'manager' || $user->isAdmin();
    }

    public function rules()
    {
        return [
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'nullable|in:low,medium,high',
            'status' => 'nullable|in:new,pending,in_progress,completed,fialed',
            'due_date' => 'nullable|date',
        ];
    }
}
