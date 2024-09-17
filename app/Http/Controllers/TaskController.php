<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use App\Models\Project;
use App\Services\TaskService;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskStatusRequest;
use App\Http\Requests\AddTaskNoteRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    protected $taskService;

    /**
     * TaskController constructor.
     * Inject the TaskService into the controller.
     *
     * @param TaskService $taskService
     */
    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    /**
     * Display all tasks for a specific project and the authenticated user.
     *
     * @param Project $project
     * @return JsonResponse
     */
    public function index(Project $project): JsonResponse
    {
        $this->authorize('view', $project);

        try {
            // Retrieve all tasks for the given project and user
            $tasks = $this->taskService->getTasksForProject($project, auth()->user());
            return response()->json($tasks);
        } catch (Exception $e) {
            // Handle exception and return error message
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created task for the specified project.
     *
     * @param StoreTaskRequest $request
     * @param Project $project
     * @return JsonResponse
     */
    public function store(StoreTaskRequest $request, Project $project): JsonResponse
    {
        try {
            // Create a new task for the project
            $task = $this->taskService->createTask($project, $request->validated());
            return response()->json(['message' => 'Task created successfully', 'task' => $task], 201);
        } catch (Exception $e) {
            // Handle exception and return error message
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update the task's status.
     *
     * @param UpdateTaskStatusRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function updateStatus(UpdateTaskStatusRequest $request, int $id): JsonResponse
    {
        try {
            // Update the task status using the provided status
            $task = $this->taskService->updateTaskStatus($id, $request->status);
            return response()->json(['message' => 'Task status updated successfully', 'task' => $task]);
        } catch (Exception $e) {
            // Handle exception and return error message
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Add a note to the specified task.
     *
     * @param AddTaskNoteRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function addNote(AddTaskNoteRequest $request, int $id): JsonResponse
    {
        try {
            // Add a note to the task
            $task = $this->taskService->addNoteToTask($id, $request->tester_notes);
            return response()->json(['message' => 'Note added to task successfully', 'task' => $task]);
        } catch (Exception $e) {
            // Handle exception and return error message
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Filter tasks by priority and status within a project.
     *
     * @param Request $request
     * @param Project $project
     * @return JsonResponse
     */
    public function filterTasks(Request $request, Project $project): JsonResponse
    {
        try {
            // Filter tasks based on priority and status
            $tasks = $this->taskService->filterTasks($project, $request->all());
            return response()->json($tasks);
        } catch (Exception $e) {
            // Handle exception and return error message
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update the task's details.
     *
     * @param UpdateTaskRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdateTaskRequest $request, int $id): JsonResponse
    {
        try {
            // Update the task with the validated request data
            $updatedTask = $this->taskService->updateTask($id, $request->validated());
            return response()->json(['message' => 'Task updated successfully', 'task' => $updatedTask]);
        } catch (Exception $e) {
            // Handle exception and return error message
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Delete the specified task.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $user = Auth::user();
        $task = Task::find($id);
        $project = $task->project;
        $role = $project->users()->where('user_id', $user->id)->first()?->pivot->role;

        // Ensure only project managers or admins can delete the task
        if ($role !== 'manager' && !$user->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            // Delete the specified task
            $this->taskService->deleteTask($task);
            return response()->json(['message' => 'Task deleted successfully']);
        } catch (Exception $e) {
            // Handle exception and return error message
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
