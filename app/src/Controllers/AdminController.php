<?php

namespace App\Controllers;

use App\Framework\Controller;
use App\Services\AdminService;

class AdminController extends Controller
{
    private AdminService $adminService;
    
    public function __construct()
    {
        $this->adminService = new AdminService();
    }

    public function users()
    {
        $this->requireAuth('admin');
        $users = $this->adminService->getAllUsers();
        $this->view('Admin/Users', ['users' => $users]);
    }

    public function editUser()
    {
        $this->requireAuth('admin');
        $userId = $_GET['id'] ?? null;

        if (!$userId) $this->redirect('/admin/users');

        $user = $this->adminService->getUser((int)$userId);
        
        if (!$user) {
            $this->redirect('/admin/users');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fname = $_POST['first_name'];
            $lname = $_POST['last_name'];
            $email = $_POST['email'];
            $bio   = $_POST['bio'] ?? '';
            
            if ($this->adminService->updateUser($user->id, $fname, $lname, $email, $user->role, $bio)) {
                $this->redirect('/admin/users');
            }
        }

        $this->view('Admin/EditUser', ['user' => $user]);
    }

    public function deleteUser()
    {
        $this->requireAuth('admin');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_POST['user_id'] ?? 0;
            $this->adminService->deleteUser((int)$userId);
            $this->redirect('/admin/users');
        }
    }
    public function deleteProfile()
    {
        $this->requireAuth('admin');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $profileId = $_POST['profile_id'] ?? 0;
            $this->adminService->deleteTutorProfile((int)$profileId);
            $this->redirect('/admin/users');
        }
    }
    public function statistics()
    {
        $this->requireAuth('admin');
        $stats = $this->adminService->getDashboardStats();
        $this->view('Admin/Statistics', ['stats' => $stats]);
    }
}