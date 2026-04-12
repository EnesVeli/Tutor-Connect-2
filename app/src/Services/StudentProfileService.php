<?php

namespace App\Services;

use App\Repositories\StudentRepository;
use App\Repositories\UserRepository;

class StudentProfileService
{
    private StudentRepository $studentRepo;
    private UserRepository $userRepo;

    public function __construct()
    {
        $this->studentRepo = new StudentRepository();
        $this->userRepo = new UserRepository();
    }

    public function getProfile(int $userId): array
    {
        $profile = $this->studentRepo->findByUserId($userId);
        $userData = $this->userRepo->findById($userId);

        return [
            'id' => $userData->id,
            'email' => $userData->email,
            'first_name' => $userData->first_name,
            'last_name' => $userData->last_name,
            'date_of_birth' => $profile ? $profile['date_of_birth'] : null,
            'bio' => $profile ? $profile['bio'] : null
        ];
    }

    public function updateProfile(int $userId, string $firstName, string $lastName, string $dob, string $bio): array
    {
        $userData = $this->userRepo->findById($userId);
        $this->userRepo->update($userId, $firstName, $lastName, $userData->email);
        $this->studentRepo->save($userId, $dob, $bio);

        return $this->getProfile($userId);
    }
}
