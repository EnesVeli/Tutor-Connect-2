<?php

namespace App\Services;

use App\Repositories\UserRepository;
use App\Repositories\BookingRepository;
use App\Repositories\TutorRepository;
use App\Repositories\ReviewRepository;

class AdminService
{
    private UserRepository $userRepo;
    private BookingRepository $bookingRepo;
    private TutorRepository $tutorRepo;

    public function __construct()
    {
        $this->userRepo = new UserRepository();
        $this->bookingRepo = new BookingRepository();
        $this->tutorRepo = new TutorRepository();
    }

    public function getAllUsers(): array
    {
        return $this->userRepo->findAllWithBio();
    }

    public function getUser(int $id): ?array
    {
        return $this->userRepo->findByIdWithBio($id);
    }

    public function updateUser(int $id, string $fname, string $lname, string $email, ?string $bio): array
    {
        $user = $this->userRepo->findByIdWithBio($id);
        if (!$user) {
            throw new \RuntimeException(json_encode(['error' => 'User not found']));
        }

        $this->userRepo->update($id, $fname, $lname, $email);
        if ($bio !== null) {
            $this->userRepo->updateBio($id, $user['role'], $bio);
        }

        return $this->userRepo->findByIdWithBio($id);
    }

    /**
     * Delete a user. Prevents self-deletion.
     */
    public function deleteUser(int $userId, int $currentAdminId): bool
    {
        if ($userId === $currentAdminId) {
            throw new \RuntimeException(json_encode(['error' => 'Cannot delete your own account']));
        }
        return $this->userRepo->delete($userId);
    }

    public function deleteTutorProfile(int $profileId): bool
    {
        $this->bookingRepo->deleteByProfileId($profileId);
        return $this->tutorRepo->delete($profileId);
    }

    public function getDashboardStats(): array
    {
        $userStats = $this->userRepo->getStatistics();
        $bookingStats = $this->bookingRepo->getStatistics();
        $tutorStats = $this->bookingRepo->getBookingsPerTutor();
        return array_merge($userStats, $bookingStats, ['tutors_list' => $tutorStats]);
    }
}