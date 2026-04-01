<?php

namespace App\Services;

use App\Repositories\BookingRepository;
use App\Repositories\TutorRepository;
use App\Repositories\UserRepository;
class BookingService
{
    private BookingRepository $bookingRepo;
    private TutorRepository $tutorRepo;
    private UserRepository $userRepo;

    public function __construct()
    {
        $this->bookingRepo = new BookingRepository();
        $this->tutorRepo = new TutorRepository();
        $this->userRepo = new UserRepository();
    }

    public function getBookingFormDetails(int $profileId): array
    {
        $tutorProfile = $this->tutorRepo->findById($profileId);
        if (!$tutorProfile) return ['tutorProfile' => null, 'tutorName' => 'Unknown'];
        $tutorUser = $this->userRepo->findById($tutorProfile->user_id);

        return [
            'tutorProfile' => $tutorProfile,
            'tutorName' => $tutorUser ? ($tutorUser->first_name . ' ' . $tutorUser->last_name) : "Unknown Tutor"
        ];
    }

    public function preparePayment(int $profileId, string $date, string $time): array
    {
        $scheduledAt = $date . ' ' . $time . ':00';
        
        $tutorProfile = $this->tutorRepo->findById($profileId);

        $dayMap = ['Sun'=>0, 'Mon'=>1, 'Tue'=>2, 'Wed'=>3, 'Thu'=>4, 'Fri'=>5, 'Sat'=>6];
        $timestamp = strtotime($scheduledAt);
        $dayNum = (int)date('w', $timestamp); 
        
        $availableStr = $tutorProfile->available_days ?? 'Mon,Tue,Wed,Thu,Fri';
        $availableDays = explode(',', $availableStr);
        $isAllowed = false;

        foreach($availableDays as $d) {
            $d = trim($d);
            if(isset($dayMap[$d]) && $dayMap[$d] === $dayNum) {
                $isAllowed = true;
                break;
            }
        }

        if (!$isAllowed) {
            die("Error: The tutor is not available on this day (" . date('l', $timestamp) . "). Please go back and select a valid date.");
        }
        $tutorUser = $this->userRepo->findById($tutorProfile->user_id);
        return [
            'scheduledAt' => $scheduledAt,
            'prettyDate' => date('l, F j, Y \a\t H:i', strtotime($scheduledAt)),
            'tutorName' => $tutorUser ? ($tutorUser->first_name . ' ' . $tutorUser->last_name) : "Unknown",
            'rate' => $tutorProfile->hourly_rate ?? 0
        ];
    }

    public function createBooking(int $studentId, int $profileId, string $scheduledAt, string $comment): bool
    {
        $profile = $this->tutorRepo->findById($profileId);
        if (!$profile) return false; 
        return $this->bookingRepo->create($studentId, $profile->user_id, $profileId, $scheduledAt, $comment);
    }

    public function getUserBookings(int $userId, string $role): array
    {
        return $this->bookingRepo->findByUserId($userId, $role);
    }

    public function updateBookingStatus(int $bookingId, string $status): bool
    {
        return $this->bookingRepo->updateStatus($bookingId, $status);
    }
}