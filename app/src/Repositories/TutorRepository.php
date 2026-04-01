<?php

namespace App\Repositories;

use App\Framework\Repository;
use App\Models\TutorProfile;
use PDO;

class TutorRepository extends Repository
{
    public function findAllByUserId(int $userId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM tutor_profiles WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_CLASS, TutorProfile::class);
    }
    public function findById(int $id): ?TutorProfile
    {
        $stmt = $this->db->prepare("SELECT * FROM tutor_profiles WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, TutorProfile::class);
        $profile = $stmt->fetch();
        return $profile ?: null;
    }

public function save(int $userId, string $bio, float $hourlyRate, int $experience, string $subject, string $start, string $end, string $days, ?int $id = null): bool
    {
        if ($id) {
            $stmt = $this->db->prepare("
                UPDATE tutor_profiles 
                SET bio = :bio, hourly_rate = :rate, experience_years = :exp, subject = :subj, 
                    availability_start = :start, availability_end = :end, available_days = :days
                WHERE id = :id AND user_id = :uid
            ");
            return $stmt->execute([
                'id' => $id, 'uid' => $userId, 'bio' => $bio, 'rate' => $hourlyRate, 
                'exp' => $experience, 'subj' => $subject, 'start' => $start, 'end' => $end, 'days' => $days
            ]);
        } else {
            $stmt = $this->db->prepare("
                INSERT INTO tutor_profiles (user_id, bio, hourly_rate, experience_years, subject, availability_start, availability_end, available_days) 
                VALUES (:uid, :bio, :rate, :exp, :subj, :start, :end, :days) 
            ");
            return $stmt->execute([
                'uid' => $userId, 'bio' => $bio, 'rate' => $hourlyRate, 
                'exp' => $experience, 'subj' => $subject, 'start' => $start, 'end' => $end, 'days' => $days
            ]);
        }
    }
    public function searchTutors(?string $subject, ?float $minPrice, ?float $maxPrice): array
    {
        $sql = "SELECT t.*, t.id as profile_id, u.first_name, u.last_name, u.email 
                FROM tutor_profiles t 
                JOIN users u ON t.user_id = u.id 
                WHERE 1=1";
        
        $params = [];
        
        if (!empty($subject)) {
            $sql .= " AND t.subject LIKE :subject";
            $params['subject'] = "%$subject%";
        }

        if ($minPrice !== null) {
            $sql .= " AND t.hourly_rate >= :min_price";
            $params['min_price'] = $minPrice;
        }

        if ($maxPrice !== null) {
            $sql .= " AND t.hourly_rate <= :max_price";
            $params['max_price'] = $maxPrice;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    public function findByUserId(int $userId): ?TutorProfile
    {
        $profiles = $this->findAllByUserId($userId);
        return $profiles[0] ?? null;
    }
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM tutor_profiles WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}