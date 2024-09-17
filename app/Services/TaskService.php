<?php

namespace App\Services;

use App\Models\Task;
use App\Models\Project;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class TaskService
{
    /**
     * Get all tasks for a specific project and user.
     *
     * This method retrieves all tasks that belong to a specific project for the given user. 
     * It leverages the relationship between the user and the project to fetch the associated tasks.
     *
     * @param Project $project The project for which tasks need to be fetched.
     * @param $user The user for whom the tasks need to be retrieved.
     * @return \Illuminate\Database\Eloquent\Collection The collection of tasks associated with the project and user.
     * @throws Exception If fetching the tasks fails.
     */
    public function getTasksForProject(Project $project, $user)
    {
        try {
            return $user->tasksThroughProject($project->id); // Fetch tasks through relationship
        } catch (Exception $e) {
            throw new Exception('Failed to fetch tasks: ' . $e->getMessage());
        }
    }

    /**
     * Create a new task for the project.
     *
     * This method creates a new task and associates it with the specified project.
     * It returns the created task instance. If any error occurs during task creation, an exception is thrown.
     *
     * @param Project $project The project to which the task will be associated.
     * @param array $data The data used to create the task.
     * @return Task The newly created task.
     * @throws Exception If task creation fails.
     */
    public function createTask(Project $project, array $data)
    {
        try {
            return $project->tasks()->create($data);
        } catch (Exception $e) {
            throw new Exception('Failed to create task: ' . $e->getMessage());
        }
    }

    /**
     * Update the status of an existing task.
     *
     * This method updates the status of a task based on the provided task ID and status string.
     * It returns a boolean indicating whether the update was successful.
     *
     * @param int $id The ID of the task to be updated.
     * @param string $status The new status of the task.
     * @return bool True if the task status was updated successfully, false otherwise.
     * @throws Exception If task status update fails.
     */
    public function updateTaskStatus($id, string $status)
    {
        try {
            $task = Task::find($id);
            return $task->update(['status' => $status]);
        } catch (Exception $e) {
            throw new Exception('Failed to update task status: ' . $e->getMessage());
        }
    }

    /**
     * Add a note to the task.
     *
     * This method updates the task by adding a note to the `tester_notes` field. 
     * It returns a boolean indicating whether the update was successful.
     *
     * @param int $id The ID of the task to which the note will be added.
     * @param string $note The note content to be added.
     * @return bool True if the note was added successfully, false otherwise.
     * @throws Exception If adding the note to the task fails.
     */
    public function addNoteToTask($id, string $note)
    {
        $task = Task::find($id);
        try {
            return $task->update(['tester_notes' => $note]);
        } catch (Exception $e) {
            throw new Exception('Failed to add note to task: ' . $e->getMessage());
        }
    }

    /**
     * Filter tasks by priority and status for a specific project.
     *
     * This method filters tasks associated with a project based on the provided priority and status filters.
     * It returns a collection of tasks that match the given criteria.
     *
     * @param Project $project The project whose tasks will be filtered.
     * @param array $filters The filters to be applied (priority, status).
     * @return \Illuminate\Database\Eloquent\Collection The filtered collection of tasks.
     * @throws Exception If filtering tasks fails.
     */
    public function filterTasks(Project $project, array $filters)
    {
        try {
            return $project->tasks()
                ->where('priority', $filters['priority'] ?? null)
                ->where('status', $filters['status'] ?? null)
                ->get();
        } catch (Exception $e) {
            throw new Exception('Failed to filter tasks: ' . $e->getMessage());
        }
    }

    /**
     * Update a task.
     *
     * This method updates a task's details using the provided task ID and data array.
     * It returns the updated task instance.
     *
     * @param int $id The ID of the task to be updated.
     * @param array $data The data to update the task with.
     * @return Task The updated task instance.
     */
    public function updateTask($id, array $data)
    {
        $task = Task::find($id);
        $task->update($data);
        return $task;
    }

    /**
     * Delete a task.
     *
     * This method deletes the specified task.
     * 
     * @param Task $task The task to be deleted.
     * @return void
     */
    public function deleteTask(Task $task)
    {
        $task->delete();
    }
}
