<?php

namespace App\Controllers;

use App\Framework\Controller;
use App\Repositories\StudentRepository;
use App\Repositories\UserRepository;

class StudentProfileController extends Controller
{
    public function edit()
    {
        $this->requireAuth('student');

        $studentRepo = new StudentRepository();
        $userRepo = new UserRepository();
        
        $profile = $studentRepo->findByUserId($_SESSION['user_id']);
        $user = $userRepo->findById($_SESSION['user_id']);
        
        $message = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $dob = $_POST['date_of_birth'];
            $bio = $_POST['bio'];
            $studentRepo->save($_SESSION['user_id'], $dob, $bio);

            $fname = $_POST['first_name'];
            $lname = $_POST['last_name'];
            $userRepo->update($_SESSION['user_id'], $fname, $lname, $user->email);

            $message = "Profile saved!";
            $profile = $studentRepo->findByUserId($_SESSION['user_id']);
            $user = $userRepo->findById($_SESSION['user_id']);
        }

        $this->view('Student/Profile', [
            'profile' => $profile,
            'user' => $user,
            'message' => $message
        ]);
    }
}