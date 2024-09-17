<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Task
 * 
 * Represents a task that is associated with a project in the system.
 * Each task has attributes such as title, description, status, priority, due date, and tester notes.
 * 
 * @package App\Models
 */
class Task extends Model
{
    use HasFactory;

    /**
     * @var array $fillable
     * The attributes that are mass assignable. These fields can be populated via mass assignment.
     */
    protected $fillable = [
        'project_id',        // The ID of the project this task belongs to
        'title',             // The title of the task
        'description',       // A detailed description of the task
        'status',            // The current status of the task (e.g., 'pending', 'in progress', 'completed')
        'priority',          // The priority level of the task (e.g., 'low', 'medium', 'high')
        'due_date',          // The due date for the task to be completed
        'tester_notes',      // Notes from the tester regarding this task
    ];

    /**
     * Define the inverse relationship between Task and Project.
     * Each task belongs to one project.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
