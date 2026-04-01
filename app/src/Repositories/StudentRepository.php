<?php

namespace App\Repositories;

use App\Framework\Repository;
use App\Models\StudentProfile;
use PDO;

class StudentRepository extends Repository
{
    public function findByUserId(int $userId): ?StudentProfile
    {
        $stmt = $this->db->prepare("SELECT * FROM student_profiles WHERE user_id = :uid");
        $stmt->execute(['uid' => $userId]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, StudentProfile::class);
        $profile = $stmt->fetch();
        return $profile ?: null;
    }

    public function save(int $userId, string $dob, string $bio): bool
    {
        $existing = $this->findByUserId($userId);

        if ($existing) {
            $stmt = $this->db->prepare("UPDATE student_profiles SET date_of_birth = :dob, bio = :bio WHERE user_id = :uid");
        } else {
            $stmt = $this->db->prepare("INSERT INTO student_profiles (user_id, date_of_birth, bio) VALUES (:uid, :dob, :bio)");
        }

        return $stmt->execute(['uid' => $userId, 'dob' => $dob, 'bio' => $bio]);
    }
}