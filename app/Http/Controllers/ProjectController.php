<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Services\ProjectService;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    protected $projectService;

    /**
     * ProjectController constructor.
     * Inject the ProjectService into the controller.
     *
     * @param ProjectService $projectService
     */
    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
    }

    /**
     * Display a listing of the user's projects.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            // Get all projects for the authenticated user
            $projects = $this->projectService->getAllProjectsForUser();
            return response()->json($projects);
        } catch (Exception $e) {
            // Handle exceptions and return a 500 error response
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified project.
     *
     * @param Project $project
     * @return JsonResponse
     */
    public function show(Project $project): JsonResponse
    {
        try {
            // Return the requested project
            return response()->json($project);
        } catch (ModelNotFoundException $e) {
            // Handle the case where the project is not found
            return response()->json(['error' => 'Project not found.'], 404);
        } catch (Exception $e) {
            // Handle other exceptions
            return response()->json(['error' => 'An error occurred.'], 500);
        }
    }

    /**
     * Store a newly created project in storage.
     *
     * @param StoreProjectRequest $request
     * @return JsonResponse
     */
    public function store(StoreProjectRequest $request): JsonResponse
    {
        try {
            // Create a new project using the validated request data
            $project = $this->projectService->createProject($request->validated());
            return response()->json(['message' => 'Project created successfully', 'project' => $project], 201);
        } catch (Exception $e) {
            // Handle exceptions during project creation
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified project in storage.
     *
     * @param UpdateProjectRequest $request
     * @param Project $project
     * @return JsonResponse
     */
    public function update(UpdateProjectRequest $request, Project $project): JsonResponse
    {
        try {
            // Update the project with the validated request data
            $this->projectService->updateProject($project, $request->validated());
            return response()->json(['message' => 'Project updated successfully', 'project' => $project]);
        } catch (Exception $e) {
            // Handle exceptions during project update
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified project from storage.
     *
     * @param Project $project
     * @return JsonResponse
     */
    public function destroy(Project $project): JsonResponse
    {
        // Get the authenticated user
        $user = Auth::user();

        try {
            // Only admin users can delete projects
            if ($user->isAdmin()) {
                $this->projectService->deleteProject($project);
                return response()->json(['message' => 'Project deleted successfully']);
            } else {
                // If the user is not an admin, return a forbidden message
                return response()->json(['message' => 'Only Admin can delete projects']);
            }
        } catch (ModelNotFoundException $e) {
            // Handle the case where the project is not found
            return response()->json(['error' => 'Project not found.'], 404);
        } catch (Exception $e) {
            // Handle other exceptions
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
