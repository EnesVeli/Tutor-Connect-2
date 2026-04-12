<?php

namespace App\Controllers;

use App\Framework\Controller;
use App\Middleware\AuthMiddleware;
use App\Services\ReviewService;
use RuntimeException;

class ReviewController extends Controller
{
    private ReviewService $reviewService;

    public function __construct()
    {
        $this->reviewService = new ReviewService();
    }

    /**
     * PUT /api/reviews/{id} — Student edits their own review.
     */
    public function update(array $vars): void
    {
        $user = AuthMiddleware::validate('student');
        $reviewId = (int) $vars['id'];
        $body = $this->getBody();

        $rating = (int) ($body['rating'] ?? 0);
        $comment = $body['comment'] ?? '';

        if ($rating < 1 || $rating > 5) {
            $this->json(['error' => 'Validation failed', 'details' => ['rating' => 'Rating must be between 1 and 5']], 400);
            return;
        }

        try {
            $updated = $this->reviewService->updateReview($reviewId, $user->user_id, $rating, $comment);
            $this->json($updated);
        } catch (RuntimeException $e) {
            $error = json_decode($e->getMessage(), true);
            $status = match($error['error'] ?? '') {
                'Forbidden' => 403,
                'Validation failed' => 400,
                'Review not found' => 404,
                default => 403
            };
            $this->json($error, $status);
            return;
        }
    }

    /**
     * DELETE /api/reviews/{id} — Student deletes own review OR admin deletes any.
     */
    public function delete(array $vars): void
    {
        $user = AuthMiddleware::validate();
        $reviewId = (int) $vars['id'];

        if (!in_array($user->role, ['student', 'admin'])) {
            $this->json(['error' => 'Forbidden'], 403);
            return;
        }

        try {
            $this->reviewService->deleteReview($reviewId, $user->user_id, $user->role);
            http_response_code(204);
            exit;
        } catch (RuntimeException $e) {
            $error = json_decode($e->getMessage(), true);
            $status = match($error['error'] ?? '') {
                'Forbidden' => 403,
                'Validation failed' => 400,
                'Review not found' => 404,
                default => 403
            };
            $this->json($error, $status);
            return;
        }
    }
}
