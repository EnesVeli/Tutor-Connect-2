<?php

namespace App\Repositories;

use App\Framework\Repository;
use PDO;

class StudentRepository extends Repository
{
    public function findByUserId(int $userId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM student_profiles WHERE user_id = :uid");
        $stmt->execute(['uid' => $userId]);
        $result = $stmt->fetch();
        return $result ?: null;
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