<?php

namespace App\Repositories;

use App\Framework\Repository;
use App\Models\TutorProfile;
use PDO;

class TutorRepository extends Repository
{
    /**
     * Search tutor profiles with filtering and pagination.
     * Returns { data, total, page, totalPages, limit }.
     */
    public function search(?string $subject, ?float $minPrice, ?float $maxPrice, int $page = 1, int $limit = 9): array
    {
        $where = "WHERE 1=1";
        $params = [];

        if (!empty($subject)) {
            $where .= " AND t.subject LIKE :subject";
            $params['subject'] = "%$subject%";
        }

        if ($minPrice !== null) {
            $where .= " AND t.hourly_rate >= :min_price";
            $params['min_price'] = $minPrice;
        }

        if ($maxPrice !== null) {
            $where .= " AND t.hourly_rate <= :max_price";
            $params['max_price'] = $maxPrice;
        }

        // Count query
        $countSql = "SELECT COUNT(*) FROM tutor_profiles t JOIN users u ON t.user_id = u.id $where";
        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        // Paginated data query
        $offset = ($page - 1) * $limit;
        $dataSql = "SELECT t.*, t.id as profile_id, u.first_name, u.last_name, u.email,
                    ROUND((SELECT AVG(r.rating) FROM reviews r WHERE r.tutor_profile_id = t.id), 1) AS avg_rating
                    FROM tutor_profiles t 
                    JOIN users u ON t.user_id = u.id 
                    $where
                    ORDER BY t.id DESC
                    LIMIT :limit OFFSET :offset";
        
        $dataStmt = $this->db->prepare($dataSql);
        foreach ($params as $key => $val) {
            $dataStmt->bindValue($key, $val);
        }
        $dataStmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $dataStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $dataStmt->execute();
        $data = $dataStmt->fetchAll();

        return [
            'data' => $data,
            'total' => $total,
            'page' => $page,
            'totalPages' => (int) ceil($total / $limit),
            'limit' => $limit
        ];
    }

    /**
     * Find a single tutor profile by ID, joined with user info.
     */
    public function findByIdWithUser(int $id): ?array
    {
        $sql = "SELECT t.*, u.first_name, u.last_name, u.email 
                FROM tutor_profiles t 
                JOIN users u ON t.user_id = u.id 
                WHERE t.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Find all profiles belonging to a specific tutor user.
     */
    public function findAllByUserId(int $userId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM tutor_profiles WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }

    /**
     * Find a single profile by ID.
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM tutor_profiles WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Create a new tutor profile. Returns the new ID.
     */
    public function create(int $userId, string $bio, float $hourlyRate, int $experience, string $subject, string $start, string $end, string $days): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO tutor_profiles (user_id, bio, hourly_rate, experience_years, subject, availability_start, availability_end, available_days) 
             VALUES (:uid, :bio, :rate, :exp, :subj, :start, :end, :days)"
        );
        $stmt->execute([
            'uid' => $userId, 'bio' => $bio, 'rate' => $hourlyRate,
            'exp' => $experience, 'subj' => $subject, 'start' => $start, 'end' => $end, 'days' => $days
        ]);
        return (int) $this->db->lastInsertId();
    }

    /**
     * Update an existing tutor profile. Checks ownership via user_id.
     */
    public function update(int $id, int $userId, string $bio, float $hourlyRate, int $experience, string $subject, string $start, string $end, string $days): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE tutor_profiles 
             SET bio = :bio, hourly_rate = :rate, experience_years = :exp, subject = :subj, 
                 availability_start = :start, availability_end = :end, available_days = :days
             WHERE id = :id AND user_id = :uid"
        );
        return $stmt->execute([
            'id' => $id, 'uid' => $userId, 'bio' => $bio, 'rate' => $hourlyRate,
            'exp' => $experience, 'subj' => $subject, 'start' => $start, 'end' => $end, 'days' => $days
        ]);
    }

    /**
     * Delete a tutor profile.
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM tutor_profiles WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Get distinct subject names for filter dropdown.
     */
    public function getDistinctSubjects(): array
    {
        $stmt = $this->db->query("SELECT DISTINCT subject FROM tutor_profiles ORDER BY subject ASC");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}