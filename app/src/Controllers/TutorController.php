<?php

namespace App\Controllers;

use App\Framework\Controller;
use App\Repositories\TutorRepository;

class TutorController extends Controller
{
    public function index()
    {
        $this->requireAuth('tutor');
        $repo = new TutorRepository();
        $profiles = $repo->findAllByUserId($_SESSION['user_id']);
        
        $this->view('Tutor/ProfileList', ['profiles' => $profiles]);
    }
    public function edit()
    {
        $this->requireAuth('tutor');
        $repo = new TutorRepository();
        $profileId = $_GET['id'] ?? null;
        $profile = null;

        if ($profileId) {
            $profile = $repo->findById((int)$profileId);
            if ($profile && $profile->user_id !== $_SESSION['user_id']) {
                die("Unauthorized access to this profile.");
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $subject = $_POST['subject'];
            $rate = (float)$_POST['hourly_rate'];
            $exp = (int)$_POST['experience_years'];
            $bio = $_POST['bio'] ?? '';
            $start = $_POST['availability_start'];
            $end = $_POST['availability_end'];
            
            $days = isset($_POST['days']) ? implode(',', $_POST['days']) : '';

            $repo->save($_SESSION['user_id'], $bio, $rate, $exp, $subject, $start, $end, $days, $profile ? $profile->id : null);
            
            $this->redirect('/profile'); 
        }      
        $this->view('Tutor/Profile', ['profile' => $profile]);

    }
}