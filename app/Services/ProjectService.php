<?php

namespace App\Services;

use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class ProjectService
{
    /**
     * Get all projects for the currently authenticated user.
     *
     * This method retrieves all the projects associated with the currently logged-in user.
     * It returns a collection of projects that belong to the authenticated user.
     *
     * @return \Illuminate\Database\Eloquent\Collection The collection of user's projects.
     */
    public function getAllProjectsForUser()
    {
        return Auth::user()->projects;
    }

    /**
     * Create a new project.
     *
     * This method creates a new project using the provided data. If the project creation
     * is successful, it returns the created project instance. In case of an error (such as
     * validation failure or database issues), an exception is thrown.
     *
     * @param array $data The project data to be used for creation.
     * @return Project The newly created project instance.
     * @throws Exception If project creation fails due to any error.
     */
    public function createProject(array $data)
    {
        try {
            return Project::create($data);
        } catch (Exception $e) {
            // Handle exception (e.g., database error, validation failure, etc.)
            throw new Exception('Failed to create project: ' . $e->getMessage());
        }
    }

    /**
     * Update an existing project.
     *
     * This method updates an existing project with the provided data. It returns a boolean value
     * indicating whether the update was successful or not. If an error occurs during the update,
     * such as a database failure or invalid data, an exception is thrown.
     *
     * @param Project $project The project instance to be updated.
     * @param array $data The data to update the project with.
     * @return bool True if the update was successful, false otherwise.
     * @throws Exception If project update fails due to any error.
     */
    public function updateProject(Project $project, array $data)
    {
        try {
            return $project->update($data);
        } catch (Exception $e) {
            throw new Exception('Failed to update project: ' . $e->getMessage());
        }
    }

    /**
     * Delete a project.
     *
     * This method deletes the specified project instance. If the project is not found, a
     * ModelNotFoundException is thrown. If the deletion fails (e.g., due to database constraints),
     * an exception is thrown. It returns a boolean or null indicating the success of the deletion.
     *
     * @param Project $project The project instance to be deleted.
     * @return bool|null True if the deletion was successful, null if it failed.
     * @throws ModelNotFoundException If the project does not exist.
     * @throws Exception If project deletion fails due to any error.
     */
    public function deleteProject(Project $project)
    {
        if (!$project) {
            throw new ModelNotFoundException('Project not found.');
        }

        try {
            return $project->delete();
        } catch (Exception $e) {
            throw new Exception('Failed to delete project: ' . $e->getMessage());
        }
    }
}
