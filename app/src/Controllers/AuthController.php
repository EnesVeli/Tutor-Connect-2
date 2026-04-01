<?php

namespace App\Controllers;

use App\Framework\Controller; // Inherit from Base Controller
use App\Services\AuthService; // Use the new Service

class AuthController extends Controller
{
    private AuthService $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    public function login()
    {
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            $error = $this->authService->attemptLogin($email, $password);

            if ($error === null) {
                $this->redirect('/');
            }
        }

        $this->view('Login', ['error' => $error]);
    }

    public function register()
    {
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $firstName = $_POST['first_name'] ?? '';
            $lastName = $_POST['last_name'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $role = $_POST['role'] ?? 'student';

            $error = $this->authService->registerUser($email, $password, $firstName, $lastName, $role);

            if ($error === null) {
                $this->redirect('/login'); // Success!
            }
        }

        $this->view('Register', ['error' => $error]);
    }

    public function logout()
    {
        $this->authService->logout();
        $this->redirect('/login');
    }
}