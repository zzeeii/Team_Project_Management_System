<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;

Route::middleware(['auth:api'])->group(function () {
  
    Route::resource('/projects', ProjectController::class);

    
    Route::get('/projects/{project}/tasks', [TaskController::class, 'index']);
    Route::post('/projects/{project}/tasks', [TaskController::class, 'store']);
    Route::put('/projects/{project}/tasks/{id}/status', [TaskController::class, 'updateStatus']);
    Route::put('/projects/{project}/tasks/{id}', [TaskController::class, 'update']);
    Route::delete('/projects/{project}/tasks/{task}', [TaskController::class, 'destroy']);
    Route::post('/projects/{project}/tasks/{id}/note', [TaskController::class, 'addNote']);
    Route::get('/projects/{project}/tasks/filter', [TaskController::class, 'filterTasks']);
    Route::post('/projects/{project}/users', [UserController::class, 'addUserToProject']);
    Route::post('/projects/{project}/users/login', [UserController::class, 'loginUserToProject']);
    Route::post('/projects/{project}/users/logout', [UserController::class, 'logoutUserFromProject']);
    Route::get('/users/{user}/projects/{project}/tasks', [UserController::class, 'userTasks']);
    Route::get('/projects/{project}/tasks/latest', [UserController::class, 'latestTask']);
    Route::get('/projects/{project}/tasks/oldest', [UserController::class, 'oldestTask']);
    Route::get('/projects/{project}/tasks/highest-priority', [UserController::class, 'highestPriorityTask']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);

});


Route::post('/login', [AuthController::class, 'login']);
Route::post('/users', [AuthController::class, 'register']);
