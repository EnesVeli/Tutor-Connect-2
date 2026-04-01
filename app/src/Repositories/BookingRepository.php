<?php

namespace App\Repositories;

use App\Framework\Repository;
use App\Models\Booking;
use PDO;

class BookingRepository extends Repository
{
    public function create(int $studentId, int $tutorId,int $profileId, string $scheduledAt, string $comment): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO bookings (student_id, tutor_id, profile_id, scheduled_at, student_comment, status) 
            VALUES (:sid, :tid, :pid, :date, :comment, 'pending')
        ");

        return $stmt->execute([
            'sid' => $studentId,
            'tid' => $tutorId,
            'pid' => $profileId,
            'date' => $scheduledAt,
            'comment' => $comment
        ]);
    }

    public function findByUserId(int $userId, string $role): array
    {        
        if ($role === 'student') {
            $sql = "SELECT b.*, u.first_name, u.last_name, u.email, tp.subject
                    FROM bookings b
                    JOIN users u ON u.id = b.tutor_id
                    LEFT JOIN tutor_profiles tp ON b.profile_id = tp.id
                    WHERE b.student_id = :uid
                    ORDER BY b.scheduled_at DESC";
        } else {
            $sql = "SELECT b.*, u.first_name, u.last_name, u.email, sp.date_of_birth, tp.subject
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
    public function updateStatus(int $bookingId, string $status): bool
    {
        $stmt = $this->db->prepare("UPDATE bookings SET status = :status WHERE id = :id");
        return $stmt->execute(['status' => $status, 'id' => $bookingId]);
    }

    public function getStatistics(): array
    {
        $totalBookings = $this->db->query("SELECT COUNT(*) FROM bookings")->fetchColumn();
        
        $earningsSql = "
            SELECT SUM(t.hourly_rate) 
            FROM bookings b 
            JOIN tutor_profiles t ON b.tutor_id = t.user_id 
            WHERE b.status = 'confirmed'
        ";
        $totalEarnings = $this->db->query($earningsSql)->fetchColumn();

        return [
            'total_bookings' => $totalBookings,
            'total_earnings' => $totalEarnings ?: 0
        ];
    }
    public function getBookingsPerTutor(): array
    {
        $sql = "SELECT u.first_name, u.last_name, COUNT(b.id) as booking_count
                FROM bookings b
                JOIN users u ON b.tutor_id = u.id
                GROUP BY b.tutor_id
                ORDER BY booking_count DESC";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_OBJ);
    }
    public function deleteByProfileId(int $profileId): bool
    {
        $stmt = $this->db->prepare("DELETE FROM bookings WHERE profile_id = :pid");
        return $stmt->execute(['pid' => $profileId]);
    }
}