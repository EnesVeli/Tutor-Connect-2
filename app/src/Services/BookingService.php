<?php

namespace App\Services;

use App\Repositories\BookingRepository;
use App\Repositories\TutorRepository;

class BookingService
{
    private BookingRepository $bookingRepo;
    private TutorRepository $tutorRepo;

    public function __construct()
    {
        $this->bookingRepo = new BookingRepository();
        $this->tutorRepo = new TutorRepository();
    }

    /**
     * Create a booking with server-side availability validation.
     * Returns the created booking array with id.
     */
    public function createBooking(int $studentId, int $profileId, string $scheduledAt, string $comment): array
    {
        $profile = $this->tutorRepo->findById($profileId);
        if (!$profile) {
            throw new \RuntimeException(json_encode(['error' => 'Tutor profile not found']));
        }

        // Validate day availability
        $dayMap = ['Sun' => 0, 'Mon' => 1, 'Tue' => 2, 'Wed' => 3, 'Thu' => 4, 'Fri' => 5, 'Sat' => 6];
        $timestamp = strtotime($scheduledAt);

        if ($timestamp === false || $timestamp <= time()) {
            throw new \RuntimeException(json_encode([
                'error' => 'Validation failed',
                'details' => ['scheduled_at' => 'Scheduled time must be a valid future date']
            ]));
        }

        $dayNum = (int) date('w', $timestamp);
        $availableDays = explode(',', $profile['available_days'] ?? 'Mon,Tue,Wed,Thu,Fri');
        $isAllowedDay = false;

        foreach ($availableDays as $d) {
            $d = trim($d);
            if (isset($dayMap[$d]) && $dayMap[$d] === $dayNum) {
                $isAllowedDay = true;
                break;
            }
        }

        if (!$isAllowedDay) {
            throw new \RuntimeException(json_encode([
                'error' => 'Validation failed',
                'details' => ['scheduled_at' => 'The tutor is not available on ' . date('l', $timestamp)]
            ]));
        }

        // Validate time within availability window
        $bookingTime = date('H:i', $timestamp);
        $startTime = $profile['availability_start'] ?? '09:00';
        $endTime = $profile['availability_end'] ?? '17:00';

        if ($bookingTime < $startTime || $bookingTime >= $endTime) {
            throw new \RuntimeException(json_encode([
                'error' => 'Validation failed',
                'details' => ['scheduled_at' => "The tutor is only available between $startTime and $endTime"]
            ]));
        }

        $bookingId = $this->bookingRepo->create($studentId, (int) $profile['user_id'], $profileId, $scheduledAt, $comment);
        return $this->bookingRepo->findById($bookingId);
    }

    /**
     * Get user bookings based on role.
     */
    public function getUserBookings(int $userId, string $role): array
    {
        return $this->bookingRepo->findByUserId($userId, $role);
    }

    /**
     * Update booking status. Verifies the tutor owns the booking.
     */
    public function updateBookingStatus(int $bookingId, string $status, int $tutorUserId): array
    {
        $booking = $this->bookingRepo->findById($bookingId);
        if (!$booking) {
            throw new \RuntimeException(json_encode(['error' => 'Booking not found']));
        }

        if ((int) $booking['tutor_id'] !== $tutorUserId) {
            throw new \RuntimeException(json_encode(['error' => 'Forbidden']));
        }

        $validStatuses = ['confirmed', 'cancelled', 'completed'];
        if (!in_array($status, $validStatuses)) {
            throw new \RuntimeException(json_encode([
                'error' => 'Validation failed',
                'details' => ['status' => 'Status must be one of: ' . implode(', ', $validStatuses)]
            ]));
        }

        $this->bookingRepo->updateStatus($bookingId, $status);
        return $this->bookingRepo->findById($bookingId);
    }
}