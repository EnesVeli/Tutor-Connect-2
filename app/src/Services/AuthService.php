<?php

namespace App\Services;

use App\Repositories\UserRepository;

class AuthService
{
    private UserRepository $userRepo;

    public function __construct()
    {
        $this->userRepo = new UserRepository();
    }

    public function attemptLogin(string $email, string $password): ?string
    {
        $user = $this->userRepo->findByEmail($email);

        if ($user && password_verify($password, $user->password)) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user->id;
            $_SESSION['user_name'] = $user->first_name;
            $_SESSION['user_role'] = $user->role;
            return null; 
        }

        return "Invalid email or password."; 
    }

    public function registerUser(string $email, string $password, string $firstName, string $lastName, string $role): ?string
    {
        if ($this->userRepo->findByEmail($email)) {
            return "Email already registered.";
        }

        if ($this->userRepo->create($email, $password, $firstName, $lastName, $role)) {
            return null; 
        }

        return "Registration failed. Please try again.";
    }

    public function logout()
    {
        session_destroy();
    }
}