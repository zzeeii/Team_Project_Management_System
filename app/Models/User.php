<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

/**
 * Class User
 * 
 * Represents the user model that handles authentication and project management functionalities.
 * Implements JWTSubject for JWT-based authentication.
 *
 * @package App\Models
 */
class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    /**
     * @var array $fillable
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
    ];

    /**
     * @var array $hidden
     * The attributes that should be hidden for arrays (e.g., when returned in JSON).
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @var array $casts
     * The attributes that should be cast to native types (e.g., date or boolean).
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Check if the user is an administrator.
     * 
     * @return bool
     */
    public function isAdmin()
    {
        return $this->is_admin == '1';
    }

    /**
     * Define the many-to-many relationship between User and Project.
     * Each user can be associated with multiple projects.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function projects()
    {
        return $this->belongsToMany(Project::class)
                    ->withPivot('role', 'contribution_hours', 'last_activity', 'logout_at', 'login_at')
                    ->withTimestamps();
    }

    /**
     * Define the has-many-through relationship between User and Task via Project.
     * A user can have many tasks through the projects they are involved in.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function tasks()
    {
        return $this->hasManyThrough(
            Task::class,   // Target model
            Project::class, // Intermediate model
            'user_id',     // Foreign key on projects table
            'project_id',  // Foreign key on tasks table
            'id',          // Local key on users table
            'id'           // Local key on projects table
        );
    }

    /**
     * Retrieve tasks that are associated with a specific project.
     * 
     * @param int $projectId The project ID to filter tasks by.
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function tasksThroughProject($projectId)
    {
        return $this->tasks()->where('project_id', $projectId)->get();
    }

    /**
     * Filter the user's tasks based on status and/or priority.
     * 
     * @param string|null $status The status to filter tasks by (optional).
     * @param string|null $priority The priority to filter tasks by (optional).
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function filterTasks($status = null, $priority = null)
    {
        return $this->tasks()
            ->when($status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->when($priority, function ($query, $priority) {
                return $query->where('priority', $priority);
            });
    }

    /**
     * Get the unique identifier for the JWT token.
     * 
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Get any custom claims for the JWT token.
     * 
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
