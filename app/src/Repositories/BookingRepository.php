<?php

namespace App\Repositories;

use App\Framework\Repository;
use PDO;

class BookingRepository extends Repository
{
    /**
     * Create a booking. Returns the new booking ID
     */
    public function create(int $studentId, int $tutorId, int $profileId, string $scheduledAt, string $comment): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO bookings (student_id, tutor_id, profile_id, scheduled_at, student_comment, status) 
             VALUES (:sid, :tid, :pid, :date, :comment, 'pending')"
        );
        $stmt->execute([
            'sid' => $studentId,
            'tid' => $tutorId,
            'pid' => $profileId,
            'date' => $scheduledAt,
            'comment' => $comment
        ]);
        return (int) $this->db->lastInsertId();
    }

    /**
     * Find a single booking by ID
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM bookings WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Find bookings for a user with related info
     */
    public function findByUserId(int $userId, string $role): array
    {
        if ($role === 'student') {
            $sql = "SELECT b.*, u.first_name, u.last_name, u.email, tp.subject, tp.hourly_rate,
                    rev.id AS review_id, rev.rating AS review_rating, rev.comment AS review_comment, rev.created_at AS review_created_at
                    FROM bookings b
                    JOIN users u ON u.id = b.tutor_id
                    LEFT JOIN tutor_profiles tp ON b.profile_id = tp.id
                    LEFT JOIN reviews rev ON rev.booking_id = b.id AND rev.student_id = b.student_id
                    WHERE b.student_id = :uid
                    ORDER BY b.scheduled_at DESC";
        } else {
            $sql = "SELECT b.*, u.first_name, u.last_name, u.email, sp.date_of_birth, tp.subject, tp.hourly_rate
                    FROM bookings b
                    JOIN users u ON u.id = b.student_id
                    LEFT JOIN student_profiles sp ON sp.user_id = u.id
                    LEFT JOIN tutor_profiles tp ON b.profile_id = tp.id
                    WHERE b.tutor_id = :uid
                    ORDER BY b.scheduled_at DESC";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['uid' => $userId]);
        return $stmt->fetchAll();
    }

    /**
     * Update booking status
     */
    public function updateStatus(int $bookingId, string $status): bool
    {
        $stmt = $this->db->prepare("UPDATE bookings SET status = :status WHERE id = :id");
        return $stmt->execute(['status' => $status, 'id' => $bookingId]);
    }

    /**
     * Get booking statistics for admin dashboard.
     */
    public function getStatistics(): array
    {
        $totalBookingsStmt = $this->db->prepare("SELECT COUNT(*) FROM bookings");
        $totalBookingsStmt->execute();
        $totalBookings = (int) $totalBookingsStmt->fetchColumn();

        $earningsSql = "SELECT COALESCE(SUM(t.hourly_rate), 0) 
                        FROM bookings b 
                        JOIN tutor_profiles t ON b.profile_id = t.id 
                        WHERE b.status IN ('confirmed', 'completed')";
        $totalEarningsStmt = $this->db->prepare($earningsSql);
        $totalEarningsStmt->execute();
        $totalEarnings = (float) $totalEarningsStmt->fetchColumn();

        return [
            'total_bookings' => $totalBookings,
            'total_earnings' => $totalEarnings
        ];
    }

    /**
     * Get bookings per tutor for popularity ranking.
     */
    public function getBookingsPerTutor(): array
    {
        $sql = "SELECT u.first_name, u.last_name, COUNT(b.id) as booking_count
                FROM bookings b
                JOIN users u ON b.tutor_id = u.id
                GROUP BY b.tutor_id, u.first_name, u.last_name
                ORDER BY booking_count DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Delete bookings by profile ID
     */
    public function deleteByProfileId(int $profileId): bool
    {
        $stmt = $this->db->prepare("DELETE FROM bookings WHERE profile_id = :pid");
        return $stmt->execute(['pid' => $profileId]);
    }
}
