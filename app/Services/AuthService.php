<?php

namespace App\Services;

use App\Models\User;
use Dotenv\Exception\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Exception;

class AuthService
{
    /**
     * Handle user login process.
     *
     * This method attempts to authenticate a user based on the provided credentials.
     * If authentication is successful, it returns the authenticated user's information
     * along with a JWT token. Otherwise, an exception is thrown.
     *
     * @param array $credentials The user's login credentials (email and password).
     * @return array An array containing the user's information and authorization details.
     * @throws Exception If the authentication fails or an error occurs during the process.
     */
    public function login(array $credentials): array
    {
        try {
            // Attempt to authenticate
            $token = Auth::attempt($credentials);

            if (!$token) {
                throw ValidationException::withMessages(['error' => 'Unauthorized: Invalid credentials']);
            }

            // Retrieve authenticated user
            $user = Auth::user();
            $projects = $user->projects;
            return [
                'status' => 'success',
                'user' => $user,
                //'projects' => $projects,
                'authorisation' => [
                    'token' => $token,
                    'type' => 'bearer',
                ],
            ];
        } catch (Exception $e) {
            // Handle exceptions and throw them back to the controller
            throw new Exception($e->getMessage(), 401);
        }
    }

    /**
     * Register a new user.
     *
     * This method creates a new user in the system and returns the user's information
     * along with a JWT token. It handles password hashing before saving the user.
     *
     * @param array $data The user's registration data, including name, email, and password.
     * @return array An array containing the user's information and authorization token.
     * @throws Exception If user registration fails or an error occurs during the process.
     */
    public function register(array $data): array
    {
        try {
            // Create the new user
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            // Return user data and token
            return [
                'user' => $user,
                'token' => Auth::login($user),
                'type' => 'bearer',
            ];
        } catch (Exception $e) {
            // Throw exception if registration fails
            throw new Exception('User registration failed: ' . $e->getMessage());
        }
    }

    /**
     * Log out the authenticated user.
     *
     * This method logs out the currently authenticated user and invalidates their JWT token.
     *
     * @return array An array containing the status and a message indicating successful logout.
     */
    public function logout(): array
    {
        Auth::logout();

        return [
            'status' => 'success',
            'message' => 'Successfully logged out',
        ];
    }

    /**
     * Refresh the JWT token for the authenticated user.
     *
     * This method generates a new JWT token for the currently authenticated user, invalidating the old token.
     *
     * @return array An array containing the status, user information, and the refreshed JWT token.
     */
    public function refresh(): array
    {
        return [
            'status' => 'success',
            'user' => Auth::user(),
            'authorisation' => [
                'token' => Auth::refresh(),
                'type' => 'bearer',
            ],
        ];
    }
}
