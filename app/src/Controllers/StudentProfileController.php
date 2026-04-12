<?php

namespace App\Controllers;

use App\Framework\Controller;
use App\Middleware\AuthMiddleware;
use App\Services\StudentProfileService;

class StudentProfileController extends Controller
{
    private StudentProfileService $studentProfileService;

    public function __construct()
    {
        $this->studentProfileService = new StudentProfileService();
    }

    /**
     * GET /api/student/profile
     */
    public function show(): void
    {
        $user = AuthMiddleware::validate('student');
        $profile = $this->studentProfileService->getProfile($user->user_id);
        $this->json($profile);
    }

    /**
     * PUT /api/student/profile
     */
    public function update(): void
    {
        $user = AuthMiddleware::validate('student');
        $body = $this->getBody();

        $fname = $body['first_name'] ?? '';
        $lname = $body['last_name'] ?? '';
        $dob = $body['date_of_birth'] ?? '';
        $bio = $body['bio'] ?? '';

        if (empty(trim($fname)) || empty(trim($lname))) {
            $this->json(['error' => 'Validation failed', 'details' => [
                'first_name' => empty(trim($fname)) ? 'Required' : null,
                'last_name' => empty(trim($lname)) ? 'Required' : null,
            ]], 400);
        }

        $updated = $this->studentProfileService->updateProfile($user->user_id, $fname, $lname, $dob, $bio);
        $this->json($updated);
    }
}
