<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Project
 * 
 * Represents a project entity in the system. A project consists of tasks and is associated
 * with multiple users through a many-to-many relationship with additional pivot fields.
 * 
 * @package App\Models
 */
class Project extends Model
{
    use HasFactory;

    /**
     * @var array $fillable
     * The attributes that are mass assignable. These fields can be populated via mass assignment.
     */
    protected $fillable = [
        'name',          // The name of the project
        'description',   // A detailed description of the project
    ];

    /**
     * Define a many-to-many relationship between Project and User with pivot fields.
     * Each project can have multiple users with additional pivot attributes like role,
     * contribution hours, last activity, login/logout times, etc.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot(
            'role',               // The role of the user in the project
            'contribution_hours',  // Number of hours contributed by the user
            'last_activity',       // Timestamp of the user's last activity in the project
            'logout_at',           // Timestamp of when the user logged out
            'login_at'             // Timestamp of when the user logged in
        )->withTimestamps();
    }

    /**
     * Define a one-to-many relationship between Project and Task.
     * Each project can have multiple tasks.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Get the latest task in the project.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function latestTask()
    {
        return $this->hasOne(Task::class)->latestOfMany();
    }

    /**
     * Get the oldest task in the project.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function oldestTask()
    {
        return $this->hasOne(Task::class)->oldestOfMany();
    }

    /**
     * Get the task with the highest priority within the project.
     * Optionally, filter by task title.
     * 
     * @param string|null $title Optional task title to filter the query.
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function highestPriorityTask($title = null)
    {
        $query = $this->hasOne(Task::class)->ofMany();

        if ($title) {
            $query->where('title', $title);
            $query->where('priority', 'high');
        }

        return $query;
    }
}
