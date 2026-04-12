<?php

namespace App\Controllers;

use App\Framework\Controller;
use App\Services\AuthService;
use App\Middleware\AuthMiddleware;
use App\Repositories\UserRepository;

class AuthController extends Controller
{
    private AuthService $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    /**
     * POST /api/auth/register
     */
    public function register(): void
    {
        $body = $this->getBody();

        $firstName = $body['first_name'] ?? '';
        $lastName = $body['last_name'] ?? '';
        $email = $body['email'] ?? '';
        $password = $body['password'] ?? '';
        $role = $body['role'] ?? 'student';

        try {
            $user = $this->authService->registerUser($email, $password, $firstName, $lastName, $role);
            $this->json($user, 201);
        } catch (\RuntimeException $e) {
            $error = json_decode($e->getMessage(), true);
            $status = isset($error['details']) ? 400 : 409;
            $this->json($error, $status);
        }
    }

    /**
     * POST /api/auth/login
     */
    public function login(): void
    {
        $body = $this->getBody();

        $email = $body['email'] ?? '';
        $password = $body['password'] ?? '';

        if (empty($email) || empty($password)) {
            $this->json(['error' => 'Email and password are required'], 400);
        }

        try {
            $result = $this->authService->attemptLogin($email, $password);
            $this->json($result, 200);
        } catch (\RuntimeException $e) {
            $error = json_decode($e->getMessage(), true);
            $status = match($error['error'] ?? '') {
                'Invalid email or password' => 401,
                'Validation failed' => 400,
                default => 401
            };
            $this->json($error, $status);
        }
    }

    /**
     * GET /api/auth/me
     */
    public function me(): void
    {
        $userData = AuthMiddleware::validate();

        $userRepo = new UserRepository();
        $user = $userRepo->findById($userData->user_id);

        if (!$user) {
            $this->json(['error' => 'User not found'], 404);
        }

        $this->json([
            'id' => $user->id,
            'email' => $user->email,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'role' => $user->role
        ]);
    }
}
