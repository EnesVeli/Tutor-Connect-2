<?php

namespace App\Controllers;

use App\Framework\Controller;
use App\Services\TutorService;
use App\Services\ReviewService;
use App\Middleware\AuthMiddleware;

class TutorController extends Controller
{
    private TutorService $tutorService;
    private ReviewService $reviewService;

    public function __construct()
    {
        $this->tutorService = new TutorService();
        $this->reviewService = new ReviewService();
    }

    /**
     * GET /api/tutors — Public tutor listing with filtering and pagination
     */
    public function list(): void
    {
        $subject = $_GET['subject'] ?? null;
        $minPrice = !empty($_GET['min_price']) ? (float) $_GET['min_price'] : null;
        $maxPrice = !empty($_GET['max_price']) ? (float) $_GET['max_price'] : null;
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $limit = max(1, min(50, (int) ($_GET['limit'] ?? 9)));

        $result = $this->tutorService->searchPaginated($subject, $minPrice, $maxPrice, $page, $limit);
        $this->json($result);
    }

    /**
     * GET /api/tutors/{id} — Public tutor detail.
     */
    public function show(array $vars): void
    {
        $id = (int) $vars['id'];
        $profile = $this->tutorService->getProfile($id);

        if (!$profile) {
            $this->json(['error' => 'Tutor profile not found'], 404);
        }

        $this->json($profile);
    }

    /**
     * GET /api/tutor/profiles — Authenticated tutor's own profiles.
     */
    public function myProfiles(): void
    {
        $user = AuthMiddleware::validate('tutor');
        $profiles = $this->tutorService->getMyProfiles($user->user_id);
        $this->json($profiles);
    }

    /**
     * POST /api/tutor/profiles — Create a new tutor profile
     */
    public function create(): void
    {
        $user = AuthMiddleware::validate('tutor');
        $body = $this->getBody();

        try {
            $profile = $this->tutorService->createProfile($user->user_id, $body);
            $this->json($profile, 201);
        } catch (\RuntimeException $e) {
            $error = json_decode($e->getMessage(), true);
            $status = isset($error['details']) ? 400 : 404;
            $this->json($error, $status);
        }
    }

    /**
     * PUT /api/tutor/profiles/{id} — Update an existing tutor profile
     */
    public function update(array $vars): void
    {
        $user = AuthMiddleware::validate('tutor');
        $id = (int) $vars['id'];
        $body = $this->getBody();

        try {
            $profile = $this->tutorService->updateProfile($id, $user->user_id, $body);
            $this->json($profile);
        } catch (\RuntimeException $e) {
            $error = json_decode($e->getMessage(), true);
            $status = match($error['error'] ?? '') {
                'Forbidden' => 403,
                'Profile not found' => 404,
                default => 400
            };
            $this->json($error, $status);
        }
    }

    /**
     * DELETE /api/tutor/profiles/{id} — Delete a tutor profile
     */
    public function delete(array $vars): void
    {
        $user = AuthMiddleware::validate('tutor');
        $id = (int) $vars['id'];

        try {
            $this->tutorService->deleteProfile($id, $user->user_id);
            http_response_code(204);
            exit;
        } catch (\RuntimeException $e) {
            $error = json_decode($e->getMessage(), true);
            $status = match($error['error'] ?? '') {
                'Forbidden' => 403,
                'Profile not found' => 404,
                default => 400
            };
            $this->json($error, $status);
        }
    }

    /**
     * GET /api/tutors/{id}/reviews — Public reviews for a tutor profile.
     */
    public function getReviews(array $vars): void
    {
        $id = (int) $vars['id'];
        $reviews = $this->reviewService->getReviewsForProfile($id);
        $this->json($reviews);
    }

    /**
     * POST /api/tutors/{id}/reviews — Student submits a review for a tutor profile. Booking ID must be provided and not previously reviewed.
     */
    public function createReview(array $vars): void
    {
        $user = AuthMiddleware::validate('student');
        $tutorProfileId = (int) $vars['id'];
        $body = $this->getBody();

        $rating = (int) ($body['rating'] ?? 0);
        $comment = $body['comment'] ?? '';
        $bookingId = (int) ($body['booking_id'] ?? 0);

        // Validate
        if ($rating < 1 || $rating > 5) {
            $this->json(['error' => 'Validation failed', 'details' => ['rating' => 'Rating must be between 1 and 5']], 400);
        }
        if ($bookingId <= 0) {
            $this->json(['error' => 'Validation failed', 'details' => ['booking_id' => 'Booking ID is required']], 400);
        }

        try {
            $review = $this->reviewService->createReview($bookingId, $user->user_id, $tutorProfileId, $rating, $comment);
            $this->json($review, 201);
        } catch (\RuntimeException $e) {
            $error = json_decode($e->getMessage(), true);
            $status = match($error['error'] ?? '') {
                'You have already reviewed this booking' => 409,
                'Validation failed' => 400,
                default => 409
            };
            $this->json($error, $status);
        }
    }
}
