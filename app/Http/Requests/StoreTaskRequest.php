<?php

namespace App\Http\Requests;

use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;

class StoreTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Only developers should be able to create tasks.
     *
     * @return bool
     */
    public function authorize()
    {
        $user = Auth::user();
        $project = $this->route('project');

     
        $role = $project->users()->where('user_id', $user->id)->first()?->pivot->role;
        return $role === 'manager' || $user->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|string|in:new,in_progress,completed,fialed',
            'priority' => 'required|string|in:low,medium,high',
            'due_date' => 'required|date',
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
            'title.required' => 'Task title is required.',
            'status.required' => 'Task status is required.',
            'priority.required' => 'Task priority is required.',
            'due_date.required' => 'Task due date is required.',
        ];
    }
}
