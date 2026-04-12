<?php

namespace App\Services;

use App\Repositories\ReviewRepository;
use RuntimeException;

class ReviewService
{
    private ReviewRepository $reviewRepo;

    public function __construct()
    {
        $this->reviewRepo = new ReviewRepository();
    }

    /**
     * Get all reviews for admin moderation.
     */
    public function getAllReviews(): array
    {
        return $this->reviewRepo->findAll();
    }

    public function getReviewsForProfile(int $tutorProfileId): array
    {
        return $this->reviewRepo->findByTutorProfileId($tutorProfileId);
    }

    public function createReview(
        int $bookingId,
        int $studentId,
        int $tutorProfileId,
        int $rating,
        string $comment
    ): object {
        $existing = $this->reviewRepo->findByBookingId($bookingId);
        if ($existing) {
            throw new RuntimeException(json_encode(['error' => 'You have already reviewed this booking']));
        }

        $id = $this->reviewRepo->create($bookingId, $studentId, $tutorProfileId, $rating, $comment);
        $review = $this->reviewRepo->findById($id);
        return (object) ($review ?? []);
    }

    /**
     * Update a review. Enforces ownership.
     */
    public function updateReview(int $reviewId, int $studentId, int $rating, string $comment): array
    {
        $success = $this->reviewRepo->update($reviewId, $studentId, $rating, $comment);

        if (!$success) {
            throw new RuntimeException(json_encode([
                'error' => 'Forbidden',
                'message' => 'Review not found or you are not the owner'
            ]));
        }

        return $this->reviewRepo->findById($reviewId);
    }

    /**
     * Delete a review. Admin can delete any, student can only delete their own.
     */
    public function deleteReview(int $reviewId, int $userId, string $role): void
    {
        if ($role === 'student') {
            $review = $this->reviewRepo->findById($reviewId);
            if (!$review || (int) $review['student_id'] !== $userId) {
                throw new RuntimeException(json_encode([
                    'error' => 'Forbidden',
                    'message' => 'Review not found or you are not the owner'
                ]));
            }
        }

        $this->reviewRepo->delete($reviewId);
    }
}
