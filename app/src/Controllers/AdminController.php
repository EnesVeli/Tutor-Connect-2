<?php

namespace App\Controllers;

use App\Framework\Controller;
use App\Services\AdminService;
use App\Middleware\AuthMiddleware;
use App\Repositories\ReviewRepository;

class AdminController extends Controller
{
    private AdminService $adminService;

    public function __construct()
    {
        $this->adminService = new AdminService();
    }

    /**
     * GET /api/admin/users
     */
    public function listUsers(): void
    {
        AuthMiddleware::validate('admin');
        $users = $this->adminService->getAllUsers();
        $this->json($users);
    }

    /**
     * GET /api/admin/users/{id}
     */
    public function getUser(array $vars): void
    {
        AuthMiddleware::validate('admin');
        $user = $this->adminService->getUser((int) $vars['id']);

        if (!$user) {
            $this->json(['error' => 'User not found'], 404);
        }

        $this->json($user);
    }

    /**
     * PUT /api/admin/users/{id}
     */
    public function updateUser(array $vars): void
    {
        AuthMiddleware::validate('admin');
        $id = (int) $vars['id'];
        $body = $this->getBody();

        $fname = $body['first_name'] ?? '';
        $lname = $body['last_name'] ?? '';
        $email = $body['email'] ?? '';
        $bio = $body['bio'] ?? null;

        if (empty(trim($fname)) || empty(trim($lname)) || empty(trim($email))) {
            $this->json(['error' => 'Validation failed', 'details' => [
                'first_name' => empty(trim($fname)) ? 'Required' : null,
                'last_name' => empty(trim($lname)) ? 'Required' : null,
                'email' => empty(trim($email)) ? 'Required' : null,
            ]], 400);
        }

        try {
            $user = $this->adminService->updateUser($id, $fname, $lname, $email, $bio);
            $this->json($user);
        } catch (\RuntimeException $e) {
            $this->json(json_decode($e->getMessage(), true), 404);
        }
    }

    /**
     * DELETE /api/admin/users/{id}
     */
    public function deleteUser(array $vars): void
    {
        $admin = AuthMiddleware::validate('admin');
        $userId = (int) $vars['id'];

        try {
            $this->adminService->deleteUser($userId, $admin->user_id);
            http_response_code(204);
            exit;
        } catch (\RuntimeException $e) {
            $error = json_decode($e->getMessage(), true);
            $this->json($error, 400);
        }
    }

    /**
     * DELETE /api/admin/tutor-profiles/{id}
     */
    public function deleteTutorProfile(array $vars): void
    {
        AuthMiddleware::validate('admin');
        $profileId = (int) $vars['id'];
        $this->adminService->deleteTutorProfile($profileId);
        http_response_code(204);
        exit;
    }

    /**
     * GET /api/admin/stats
     */
    public function stats(): void
    {
        AuthMiddleware::validate('admin');
        $stats = $this->adminService->getDashboardStats();
        $this->json($stats);
    }

    /**
     * GET /api/admin/reviews — Admin review moderation listing
     */
    public function reviews(): void
    {
        AuthMiddleware::validate('admin');
        $reviewService = new \App\Services\ReviewService();
        $this->json($reviewService->getAllReviews());
    }
}