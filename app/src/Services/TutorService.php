<?php

namespace App\Services;

use App\Repositories\TutorRepository;

class TutorService
{
    private TutorRepository $tutorRepo;

    public function __construct()
    {
        $this->tutorRepo = new TutorRepository();
    }

    /**
     * Search tutors with filtering and pagination.
     */
    public function searchPaginated(?string $subject, ?float $minPrice, ?float $maxPrice, int $page = 1, int $limit = 9): array
    {
        return $this->tutorRepo->search($subject, $minPrice, $maxPrice, $page, $limit);
    }

    /**
     * Get a single tutor profile with user info for the detail page.
     */
    public function getProfile(int $id): ?array
    {
        return $this->tutorRepo->findByIdWithUser($id);
    }

    /**
     * Get all profiles for a specific tutor user.
     */
    public function getMyProfiles(int $userId): array
    {
        return $this->tutorRepo->findAllByUserId($userId);
    }

    /**
     * Create a new tutor profile. Returns the created profile with id.
     */
    public function createProfile(int $userId, array $data): array
    {
        $errors = $this->validateProfileData($data);
        if (!empty($errors)) {
            throw new \RuntimeException(json_encode(['error' => 'Validation failed', 'details' => $errors]));
        }

        $id = $this->tutorRepo->create(
            $userId,
            $data['bio'] ?? '',
            (float) $data['hourly_rate'],
            (int) $data['experience_years'],
            $data['subject'],
            $data['availability_start'],
            $data['availability_end'],
            $data['available_days']
        );

        $profile = $this->tutorRepo->findById($id);
        return $profile;
    }

    /**
     * Update an existing tutor profile. Verifies ownership.
     */
    public function updateProfile(int $id, int $userId, array $data): array
    {
        $profile = $this->tutorRepo->findById($id);
        if (!$profile) {
            throw new \RuntimeException(json_encode(['error' => 'Profile not found']));
        }
        if ((int) $profile['user_id'] !== $userId) {
            throw new \RuntimeException(json_encode(['error' => 'Forbidden']));
        }

        $errors = $this->validateProfileData($data);
        if (!empty($errors)) {
            throw new \RuntimeException(json_encode(['error' => 'Validation failed', 'details' => $errors]));
        }

        $this->tutorRepo->update(
            $id, $userId,
            $data['bio'] ?? '',
            (float) $data['hourly_rate'],
            (int) $data['experience_years'],
            $data['subject'],
            $data['availability_start'],
            $data['availability_end'],
            $data['available_days']
        );

        return $this->tutorRepo->findById($id);
    }

    /**
     * Delete a tutor profile. Verifies ownership.
     */
    public function deleteProfile(int $id, int $userId): void
    {
        $profile = $this->tutorRepo->findById($id);
        if (!$profile) {
            throw new \RuntimeException(json_encode(['error' => 'Profile not found']));
        }
        if ((int) $profile['user_id'] !== $userId) {
            throw new \RuntimeException(json_encode(['error' => 'Forbidden']));
        }

        $this->tutorRepo->delete($id);
    }

    /**
     * Validate tutor profile form data.
     */
    private function validateProfileData(array $data): array
    {
        $errors = [];
        if (empty(trim($data['subject'] ?? ''))) $errors['subject'] = 'Subject is required';
        if (!isset($data['hourly_rate']) || (float) $data['hourly_rate'] <= 0) $errors['hourly_rate'] = 'Hourly rate must be a positive number';
        if (!isset($data['experience_years']) || (int) $data['experience_years'] < 0) $errors['experience_years'] = 'Experience must be a non-negative integer';
        if (empty(trim($data['availability_start'] ?? ''))) $errors['availability_start'] = 'Start time is required';
        if (empty(trim($data['availability_end'] ?? ''))) $errors['availability_end'] = 'End time is required';
        if (empty(trim($data['available_days'] ?? ''))) $errors['available_days'] = 'At least one available day is required';
        
        // Validate end time is after start time
        if (!empty($data['availability_start']) && !empty($data['availability_end'])) {
            if ($data['availability_end'] <= $data['availability_start']) {
                $errors['availability_end'] = 'End time must be after start time';
            }
        }

        return $errors;
    }
}