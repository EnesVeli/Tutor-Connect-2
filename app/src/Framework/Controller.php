<?php

namespace App\Framework;

abstract class Controller
{
    protected function view(string $viewPath, array $data = [])
    {
        extract($data);

        $fullPath = __DIR__ . '/../Views/' . $viewPath . '.php';

        if (file_exists($fullPath)) {
            require $fullPath;
        } else {
            die("View file not found: " . $viewPath);
        }
    }
    protected function redirect(string $url)
    {
        if (!headers_sent()) {
            header("Location: $url");
            exit;
        } else {
            echo "<script>window.location.href='$url';</script>";
            exit;
        }
    }
    
    protected function requireAuth(?string $requiredRole = null) 
    {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
        }

        if ($requiredRole && ($_SESSION['user_role'] ?? '') !== $requiredRole) {
            die("403 - Access Denied: You need to be a $requiredRole.");
        }
    }
}