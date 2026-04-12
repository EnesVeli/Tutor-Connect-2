<?php

namespace App\Controllers;

use App\Framework\Controller;
use App\Services\BookingService;
use App\Middleware\AuthMiddleware;

class BookingController extends Controller
{
    private BookingService $bookingService;

    public function __construct()
    {
        $this->bookingService = new BookingService();
    }

    /**
     * GET /api/bookings — Get bookings for the authenticated user.
     */
    public function index(): void
    {
        $user = AuthMiddleware::validate();
        
        if (!in_array($user->role, ['student', 'tutor'])) {
            $this->json(['error' => 'Forbidden'], 403);
        }

        $bookings = $this->bookingService->getUserBookings($user->user_id, $user->role);
        $this->json(['data' => $bookings, 'total' => count($bookings)]);
    }

    /**
     * POST /api/bookings — Create a new booking (student only).
     * Student ID comes from the JWT, never from the request body.
     */
    public function create(): void
    {
        $user = AuthMiddleware::validate('student');
        $body = $this->getBody();

        $profileId = (int) ($body['tutor_profile_id'] ?? 0);
        $scheduledAt = $body['scheduled_at'] ?? '';
        $comment = $body['student_comment'] ?? '';

        if ($profileId <= 0 || empty($scheduledAt)) {
            $this->json([
                'error' => 'Validation failed',
                'details' => [
                    'tutor_profile_id' => $profileId <= 0 ? 'Tutor profile ID is required' : null,
                    'scheduled_at' => empty($scheduledAt) ? 'Scheduled time is required' : null
                ]
            ], 400);
        }

        try {
            $booking = $this->bookingService->createBooking($user->user_id, $profileId, $scheduledAt, $comment);
            $this->json($booking, 201);
        } catch (\RuntimeException $e) {
            $error = json_decode($e->getMessage(), true);
            $status = match($error['error'] ?? '') {
                'Tutor profile not found' => 404,
                'Forbidden' => 403,
                'Validation failed' => 400,
                default => 422
            };
            $this->json($error, $status);
        }
    }

    /**
     * PUT /api/bookings/{id} — Update booking status
     */
    public function update(array $vars): void
    {
        $user = AuthMiddleware::validate('tutor');
        $bookingId = (int) $vars['id'];
        $body = $this->getBody();
        $status = $body['status'] ?? '';

        if (empty($status)) {
            $this->json(['error' => 'Status is required'], 400);
        }

        try {
            $booking = $this->bookingService->updateBookingStatus($bookingId, $status, $user->user_id);
            $this->json($booking);
        } catch (\RuntimeException $e) {
            $error = json_decode($e->getMessage(), true);
            $status = match($error['error'] ?? '') {
                'Forbidden' => 403,
                'Booking not found' => 404,
                default => 400
            };
            $this->json($error, $status);
        }
    }
}
