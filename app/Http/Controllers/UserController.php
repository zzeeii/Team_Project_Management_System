<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddUserToProjectRequest;
use App\Http\Requests\ProjectUserRequest;
use App\Http\Requests\UserTaskRequest;
use App\Services\UserService;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $userService;

    /**
     * UserController constructor.
     * Inject the UserService into the controller.
     *
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Get user tasks through a specific project.
     *
     * @param UserTaskRequest $request
     * @param User $user
     * @return JsonResponse
     */
    public function userTasks(UserTaskRequest $request, User $user): JsonResponse
    {
        try {
            $tasks = $this->userService->getUserTasksThroughProject($user, $request->project_id);
            return response()->json($tasks);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'An error occurred while fetching user tasks.'], 500);
        }
    }

    /**
     * Get the latest task for a specific project.
     *
     * @param Project $project
     * @return JsonResponse
     */
    public function latestTask(Project $project): JsonResponse
    {
        try {
            $task = $this->userService->getLatestTask($project);
            return response()->json($task);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'An error occurred while fetching the latest task.'], 500);
        }
    }

    /**
     * Get the oldest task for a specific project.
     *
     * @param Project $project
     * @return JsonResponse
     */
    public function oldestTask(Project $project): JsonResponse
    {
        try {
            $task = $this->userService->getOldestTask($project);
            return response()->json($task);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'An error occurred while fetching the oldest task.'], 500);
        }
    }

    /**
     * Get the highest priority task for a project, with an optional title filter.
     *
     * @param Project $project
     * @param string|null $title
     * @return JsonResponse
     */
    public function highestPriorityTask(Project $project, ?string $title = null): JsonResponse
    {
        try {
            $task = $this->userService->getHighestPriorityTask($project, $title);
            return response()->json($task);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'An error occurred while fetching the highest priority task.'], 500);
        }
    }

    /**
     * Add a user to a specific project.
     *
     * @param AddUserToProjectRequest $request
     * @param Project $project
     * @return JsonResponse
     */
    public function addUserToProject(AddUserToProjectRequest $request, Project $project): JsonResponse
    {
        try {
            $user = User::findOrFail($request->input('user_id'));
            $role = $request->input('role');

            $this->userService->addUserToProject($project, $user, $role);

            return response()->json(['message' => 'User added to project successfully.']);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Log in the authenticated user to a specific project.
     *
     * @param ProjectUserRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function loginUserToProject(ProjectUserRequest $request, int $id): JsonResponse
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'User not authenticated.'], 401);
            }

            $project = Project::findOrFail($id);

            $this->userService->loginUserToProject($project, $user);

            return response()->json(['message' => 'User logged in to project successfully.'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Project not found.'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Log out the authenticated user from a specific project.
     *
     * @param ProjectUserRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function logoutUserFromProject(ProjectUserRequest $request, int $id): JsonResponse
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'User not authenticated.'], 401);
            }

            $project = Project::findOrFail($id);

            $this->userService->logoutUserFromProject($project, $user);

            return response()->json(['message' => 'User logged out from project successfully.']);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Delete a specific user by ID. Only admin users can delete.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $user = Auth::user();
        try {
            if ($user->isAdmin()) {
                $this->userService->deleteUser($id);
                return response()->json(['message' => 'User deleted successfully.']);
            } else {
                return response()->json(['message' => 'Only Admin can delete Users.'], 403);
            }
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
