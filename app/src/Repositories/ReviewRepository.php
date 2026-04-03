<?php

namespace App\Repositories;

use App\Framework\Repository;
use PDO;

class ReviewRepository extends Repository
{
    /**
     * Find all reviews for a tutor profile, with student name
     */
    public function findByTutorProfileId(int $tutorProfileId): array
    {
        $sql = "SELECT r.*, u.first_name, u.last_name 
                FROM reviews r 
                JOIN users u ON r.student_id = u.id 
                WHERE r.tutor_profile_id = :tpid 
                ORDER BY r.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['tpid' => $tutorProfileId]);
        return $stmt->fetchAll();
    }

    /**
     * Find a review by booking ID (to check if a booking has already been reviewed)
     */
    public function findByBookingId(int $bookingId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM reviews WHERE booking_id = :bid");
        $stmt->execute(['bid' => $bookingId]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Find a single review by ID
     */
    public function findById(int $id): ?array
    {
        $sql = "SELECT r.*, u.first_name, u.last_name 
                FROM reviews r 
                JOIN users u ON r.student_id = u.id 
                WHERE r.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /*
     * Create a review. Returns the new review ID
     */
    public function create(int $bookingId, int $studentId, int $tutorProfileId, int $rating, string $comment): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO reviews (booking_id, student_id, tutor_profile_id, rating, comment) 
             VALUES (:bid, :sid, :tpid, :rating, :comment)"
        );
        $stmt->execute([
            'bid' => $bookingId,
            'sid' => $studentId,
            'tpid' => $tutorProfileId,
            'rating' => $rating,
            'comment' => $comment
        ]);
        return (int) $this->db->lastInsertId();
    }

    /**
     * Delete a review 
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM reviews WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Get average rating for a tutor profile
     */
    public function getAverageRating(int $tutorProfileId): ?float
    {
        $stmt = $this->db->prepare(
            "SELECT AVG(rating) FROM reviews WHERE tutor_profile_id = :tpid"
        );
        $stmt->execute(['tpid' => $tutorProfileId]);
        $avg = $stmt->fetchColumn();
        return $avg !== false && $avg !== null ? round((float) $avg, 1) : null;
    }

    /**
     * Update a review. Enforces ownership via student_id in WHERE clause.
     * Returns true only if exactly 1 row was updated
     */
    public function update(int $id, int $studentId, int $rating, string $comment): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE reviews SET rating = :rating, comment = :comment WHERE id = :id AND student_id = :sid"
        );
        $stmt->execute(['rating' => $rating, 'comment' => $comment, 'id' => $id, 'sid' => $studentId]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Find all reviews with student name, tutor name, and subject (for admin moderation).
     */
    public function findAll(): array
    {
        $sql = "SELECT reviews.*,
                s.first_name AS student_first_name, s.last_name AS student_last_name,
                t.first_name AS tutor_first_name, t.last_name AS tutor_last_name,
                tp.subject
                FROM reviews
                JOIN users s ON reviews.student_id = s.id
                JOIN tutor_profiles tp ON reviews.tutor_profile_id = tp.id
                JOIN users t ON tp.user_id = t.id
                ORDER BY reviews.created_at DESC";
        return $this->db->query($sql)->fetchAll();
    }
}
