<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use Illuminate\Http\JsonResponse;
use Exception;

class AuthController extends Controller
{
    protected $authService;

    /**
     * Constructor to inject AuthService dependency into the controller.
     *
     * @param AuthService $authService The authentication service for handling login and registration logic.
     */
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    /**
     * Handle the login request for users.
     * @param LoginRequest $request The incoming request containing user login credentials.
     * @return JsonResponse The JSON response containing the authentication token or error message.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            // Retrieve the login credentials and pass to the AuthService
            $credentials = $request->only('email', 'password');
            $response = $this->authService->login($credentials);

            return response()->json($response);

        } catch (Exception $e) {
            // Return a JSON response in case of login failure
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], $e->getCode() ?: 401);
        }
    }

    /**
     * Handle the registration request for new users.
     * @param RegisterRequest $request The incoming request containing new user details.
     * @return JsonResponse The JSON response confirming the user creation or an error message.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            // Pass the validated registration data to the AuthService
            $data = $this->authService->register($request->validated());
            return response()->json([
                'status' => 'success',
                'message' => 'User created successfully',
                'user' => $data['user'],
                'authorisation' => [
                    'token' => $data['token'],
                    'type' => 'bearer',
                ]
            ], 201);
        } catch (Exception $e) {
            // Return a JSON response in case of registration failure
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], $e->getCode() ?: 500);
        }
    }

    /**
     * Log the authenticated user out by invalidating the JWT token.
     *
     * @return JsonResponse The JSON response confirming logout success.
     */
    public function logout(): JsonResponse
    {
        // Invalidate the user's JWT token via AuthService and return response
        $response = $this->authService->logout();
        return response()->json($response);
    }

    /**
     * Refresh the JWT token for the authenticated user.
     *
     * This is used to provide a new token without requiring the user to log in again.
     *
     * @return JsonResponse The JSON response containing the new authentication token.
     */
    public function refresh(): JsonResponse
    {
        // Refresh the JWT token via AuthService and return response
        $response = $this->authService->refresh();
        return response()->json($response);
    }
}
