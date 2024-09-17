<?php

namespace App\Http\Requests;

use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;

class AddTaskNoteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Only managers should be able to add notes to tasks.
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

 
        return $role === 'tester';

       
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'tester_notes' => 'required|string',
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
            'tester_notes.required' => 'A note is required.',
        ];
    }
}
