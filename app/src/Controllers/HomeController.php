<?php

namespace App\Controllers;

use App\Framework\Controller; 

class HomeController extends Controller
{
    public function index()
    {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
        }

        $name = $_SESSION['user_name'];
        $role = $_SESSION['user_role'];
        
        $this->view('Dashboard', ['name' => $name, 'role' => $role]);
    }
}