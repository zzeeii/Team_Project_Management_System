<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class UserService
{
    /**
     * Get tasks through a specific project for the user.
     *
     * This method retrieves all tasks for the given user associated with a specific project.
     * If no tasks are found, a `ModelNotFoundException` is thrown.
     *
     * @param User $user The user whose tasks are being retrieved.
     * @param int $projectId The ID of the project for which tasks are being fetched.
     * @return mixed The collection of tasks associated with the project and user.
     * @throws ModelNotFoundException If no tasks are found for the given project.
     */
    public function getUserTasksThroughProject(User $user, int $projectId)
    {
        $tasks = $user->tasksThroughProject($projectId);

        if ($tasks->isEmpty()) {
            throw new ModelNotFoundException('No tasks found for the given project.');
        }

        return $tasks;
    }

    /**
     * Get the latest task for the given project.
     *
     * This method retrieves the most recently created task for a specific project.
     * If no tasks are found, a `ModelNotFoundException` is thrown.
     *
     * @param Project $project The project for which the latest task is being fetched.
     * @return Task|null The latest task for the project.
     * @throws ModelNotFoundException If no tasks are found for the project.
     */
    public function getLatestTask(Project $project)
    {
        $task = $project->latestTask()->first();

        if (!$task) {
            throw new ModelNotFoundException('No tasks found for this project.');
        }

        return $task;
    }

    /**
     * Get the oldest task for the given project.
     *
     * This method retrieves the first (oldest) task created for a specific project.
     * If no tasks are found, a `ModelNotFoundException` is thrown.
     *
     * @param Project $project The project for which the oldest task is being fetched.
     * @return Task|null The oldest task for the project.
     * @throws ModelNotFoundException If no tasks are found for the project.
     */
    public function getOldestTask(Project $project)
    {
        $task = $project->oldestTask()->first();

        if (!$task) {
            throw new ModelNotFoundException('No tasks found for this project.');
        }

        return $task;
    }

    /**
     * Get the task with the highest priority, optionally filtered by title.
     *
     * This method fetches the task with the highest priority for a specific project.
     * An optional title filter can be applied. If no tasks are found, a `ModelNotFoundException` is thrown.
     *
     * @param Project $project The project for which the task is being fetched.
     * @param string|null $title An optional title filter for the task.
     * @return Task|null The task with the highest priority.
     * @throws ModelNotFoundException If no tasks with the highest priority are found.
     */
    public function getHighestPriorityTask(Project $project, ?string $title = null)
    {
        $task = $project->highestPriorityTask($title)->first();

        if (!$task) {
            throw new ModelNotFoundException('No task found with the highest priority.');
        }

        return $task;
    }

    /**
     * Assign a user to a project with a specific role.
     *
     * This method assigns a user to a given project with the specified role.
     * If the user is already assigned to the project, an exception is thrown.
     *
     * @param Project $project The project to which the user will be assigned.
     * @param User $user The user to be assigned to the project.
     * @param string $role The role of the user in the project.
     * @throws Exception If the user is already assigned to the project.
     */
    public function addUserToProject(Project $project, User $user, string $role)
    {
        if ($project->users()->where('user_id', $user->id)->exists()) {
            throw new Exception('User is already assigned to this project.');
        }

        $project->users()->attach($user->id, [
            'role' => $role,
            'contribution_hours' => 0,
            'last_activity' => now(),
            'login_at' => null,
            'logout_at' => null,
        ]);
    }

    /**
     * Log the user into a project.
     *
     * This method records the login time for a user in a specific project.
     * If the user is not assigned to the project, an exception is thrown.
     *
     * @param Project $project The project the user is logging into.
     * @param User $user The user logging into the project.
     * @throws Exception If the user is not assigned to the project.
     */
    public function loginUserToProject(Project $project, User $user)
    {
        $projectUser = $project->users()->where('user_id', $user->id)->first();

        if (!$projectUser) {
            throw new Exception('User is not assigned to this project.');
        }

        $project->users()->updateExistingPivot($user->id, [
            'login_at' => Carbon::now(),
            'logout_at' => null, // Reset the logout time on login
        ]);
    }

    /**
     * Log the user out from a project.
     *
     * This method records the logout time and calculates the contribution hours for a user
     * based on the time between login and logout. If the user is not assigned or logged in, an exception is thrown.
     *
     * @param Project $project The project the user is logging out from.
     * @param User $user The user logging out from the project.
     * @throws Exception If the user is not assigned or logged in.
     */
    public function logoutUserFromProject(Project $project, User $user)
    {
        $projectUser = $project->users()->where('user_id', $user->id)->first();

        if (!$projectUser) {
            throw new Exception('User is not assigned to this project.');
        }

        $loginAt = $projectUser->pivot->login_at;

        if ($loginAt) {
            $currentContributionHours = $projectUser->pivot->contribution_hours;
            $logoutAt = Carbon::now();

            $minutesWorked = Carbon::parse($loginAt)->diffInMinutes($logoutAt);
      

            $project->users()->updateExistingPivot($user->id, [
                'contribution_hours' => $currentContributionHours + $minutesWorked,
                'logout_at' => $logoutAt,
                'login_at' => null,
            ]);
        } else {
            throw new Exception('User is not logged in.');
        }
    }

    /**
     * Delete a user by ID.
     *
     * This method deletes a user based on the provided user ID. If the user does not exist,
     * an exception is thrown.
     *
     * @param int $id The ID of the user to be deleted.
     * @throws Exception If the user is not found or deletion fails.
     */
    public function deleteUser($id): void
    {
        $user = User::find($id);
        try {
            if (!$user) {
                throw new Exception('User not found.');
            }

            $user->delete();
        } catch (Exception $e) {
            throw new Exception('Failed to delete user: ' . $e->getMessage());
        }
    }
}
