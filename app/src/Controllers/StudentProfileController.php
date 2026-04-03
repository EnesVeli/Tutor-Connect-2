<?php

namespace App\Controllers;

use App\Framework\Controller;
use App\Middleware\AuthMiddleware;
use App\Repositories\StudentRepository;
use App\Repositories\UserRepository;

class StudentProfileController extends Controller
{
    /**
     * GET /api/student/profile
     */
    public function show(): void
    {
        $user = AuthMiddleware::validate('student');

        $studentRepo = new StudentRepository();
        $userRepo = new UserRepository();

        $profile = $studentRepo->findByUserId($user->user_id);
        $userData = $userRepo->findById($user->user_id);

        $this->json([
            'id' => $userData->id,
            'email' => $userData->email,
            'first_name' => $userData->first_name,
            'last_name' => $userData->last_name,
            'date_of_birth' => $profile ? $profile['date_of_birth'] : null,
            'bio' => $profile ? $profile['bio'] : null
        ]);
    }

    /**
     * PUT /api/student/profile
     */
    public function update(): void
    {
        $user = AuthMiddleware::validate('student');
        $body = $this->getBody();

        $studentRepo = new StudentRepository();
        $userRepo = new UserRepository();

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

        $userData = $userRepo->findById($user->user_id);
        $userRepo->update($user->user_id, $fname, $lname, $userData->email);
        $studentRepo->save($user->user_id, $dob, $bio);

        // Return updated data
        $profile = $studentRepo->findByUserId($user->user_id);
        $updatedUser = $userRepo->findById($user->user_id);

        $this->json([
            'id' => $updatedUser->id,
            'email' => $updatedUser->email,
            'first_name' => $updatedUser->first_name,
            'last_name' => $updatedUser->last_name,
            'date_of_birth' => $profile ? $profile['date_of_birth'] : null,
            'bio' => $profile ? $profile['bio'] : null
        ]);
    }
}