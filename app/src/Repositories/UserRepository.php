<?php

namespace App\Repositories;

use App\Framework\Repository;
use App\Models\User;
use PDO;

class UserRepository extends Repository
{
    public function findByEmail(string $email): ?User
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, User::class);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function findById(int $id): ?User
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, User::class);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function create(string $email, string $password, string $firstName, string $lastName, string $role): bool
    {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->db->prepare("INSERT INTO users (email, password, first_name, last_name, role) VALUES (:email, :pass, :fname, :lname, :role)");
        return $stmt->execute(['email' => $email, 'pass' => $hash, 'fname' => $firstName, 'lname' => $lastName, 'role' => $role]);
    }

    public function update(int $id, string $firstName, string $lastName, string $email): bool
    {
        $stmt = $this->db->prepare("UPDATE users SET first_name = :fname, last_name = :lname, email = :email WHERE id = :id");
        return $stmt->execute(['fname' => $firstName, 'lname' => $lastName, 'email' => $email, 'id' => $id]);
    }

    public function delete(int $id): bool
    {
        try {
            $this->db->beginTransaction();
            $this->db->prepare("DELETE FROM bookings WHERE student_id = :id OR tutor_id = :id")->execute(['id' => $id]);
            $this->db->prepare("DELETE FROM tutor_profiles WHERE user_id = :id")->execute(['id' => $id]);
            $this->db->prepare("DELETE FROM student_profiles WHERE user_id = :id")->execute(['id' => $id]);
            
            $stmt = $this->db->prepare("DELETE FROM users WHERE id = :id");
            $success = $stmt->execute(['id' => $id]);
            
            $this->db->commit();
            return $success;
        } catch (\Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
    public function findAllWithBio(): array
    {
        $sql = "SELECT u.*, COALESCE(t.bio, s.bio, 'No bio') as bio,
                t.id as profile_id,
                t.subject
                FROM users u
                LEFT JOIN tutor_profiles t ON u.id = t.user_id
                LEFT JOIN student_profiles s ON u.id = s.user_id
                ORDER BY u.id DESC";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function getStatistics(): array
    {
        return [
            'total_users' => $this->db->query("SELECT COUNT(*) FROM users")->fetchColumn(),
            'total_tutors' => $this->db->query("SELECT COUNT(*) FROM users WHERE role = 'tutor'")->fetchColumn(),
            'total_students' => $this->db->query("SELECT COUNT(*) FROM users WHERE role = 'student'")->fetchColumn(),
        ];
    }
    public function findByIdWithBio(int $id): ?object
    {
        $sql = "SELECT u.*, COALESCE(t.bio, s.bio, '') as bio 
                FROM users u
                LEFT JOIN tutor_profiles t ON u.id = t.user_id
                LEFT JOIN student_profiles s ON u.id = s.user_id
                WHERE u.id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_OBJ) ?: null;
    }

    public function updateBio(int $userId, string $role, ?string $bio): bool
    {
        if ($role === 'student') {
            $sql = "UPDATE student_profiles SET bio = :bio WHERE user_id = :id";
        } elseif ($role === 'tutor') {
            $sql = "UPDATE tutor_profiles SET bio = :bio WHERE user_id = :id";
        } else {
            return true; 
        }

        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['bio' => $bio, 'id' => $userId]);
    }
}