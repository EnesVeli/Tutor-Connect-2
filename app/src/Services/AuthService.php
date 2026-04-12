<?php

namespace App\Services;

use App\Config;
use App\Repositories\UserRepository;
use Firebase\JWT\JWT;

class AuthService
{
    private UserRepository $userRepo;

    public function __construct()
    {
        $this->userRepo = new UserRepository();
    }

    /**
     * Register a new user. Returns the created user data array (no password).
     * Throws \RuntimeException on validation failure.
     */
    public function registerUser(string $email, string $password, string $firstName, string $lastName, string $role): array
    {
        // Validate
        $errors = [];
        if (empty(trim($firstName))) $errors['first_name'] = 'First name is required';
        if (empty(trim($lastName))) $errors['last_name'] = 'Last name is required';
        if (empty(trim($email)) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Valid email is required';
        if (strlen($password) < 8) $errors['password'] = 'Password must be at least 8 characters';
        if (!in_array($role, ['student', 'tutor'])) $errors['role'] = 'Role must be student or tutor';

        if (!empty($errors)) {
            throw new \RuntimeException(json_encode(['error' => 'Validation failed', 'details' => $errors]));
        }

        // Check uniqueness
        if ($this->userRepo->findByEmail($email)) {
            throw new \RuntimeException(json_encode(['error' => 'Email already registered']));
        }

        $id = $this->userRepo->create($email, $password, $firstName, $lastName, $role);

        return [
            'id' => $id,
            'email' => $email,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'role' => $role
        ];
    }

    /**
     * Attempt login. Returns { token, user } on success.
     * Throws \RuntimeException on invalid credentials.
     */
    public function attemptLogin(string $email, string $password): array
    {
        $user = $this->userRepo->findByEmail($email);

        if (!$user || !password_verify($password, $user->password)) {
            throw new \RuntimeException(json_encode(['error' => 'Invalid email or password']));
        }

        $token = $this->generateToken($user->id, $user->role, $user->email);

        return [
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'role' => $user->role
            ]
        ];
    }

    /**
     * Generate a signed JWT token with 1-hour expiry.
     */
    private function generateToken(int $userId, string $role, string $email): string
    {
        $payload = [
            'iss' => 'tutor-connect',
            'iat' => time(),
            'exp' => time() + 3600, // 1 hour
            'data' => [
                'user_id' => $userId,
                'role' => $role,
                'email' => $email
            ]
        ];

        return JWT::encode($payload, Config::jwtSecret(), 'HS256');
    }
}
